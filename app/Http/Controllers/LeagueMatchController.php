<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupsRequest;
use App\Http\Requests\GenerateFixturesRequest;
use App\Models\League;
use App\Models\LeagueGroup;
use App\Models\Fixture;
use App\Models\LeaguePlayer;
use App\Models\Ground;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeagueMatchController extends Controller
{
    public function index(League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to league');
        }

        if ($league->status !== 'auction_completed') {
            return redirect()->route('leagues.show', $league)
                ->with('error', 'League match setup is only available after auction completion.');
        }

        $leagueTeams = $league->leagueTeams()->with('team')->get();
        $existingGroups = $league->leagueGroups()->with('leagueTeams.team')->get();
        
        // Get teams that are not assigned to any group
        $assignedTeamIds = $existingGroups->flatMap(function ($group) {
            return $group->leagueTeams->pluck('id');
        })->toArray();
        
        $availableTeams = $leagueTeams->whereNotIn('id', $assignedTeamIds);

        return view('leagues.league-match.index', compact('league', 'leagueTeams', 'existingGroups', 'availableTeams'));
    }

    public function fixtureSetup(League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to league');
        }

        $groups = $league->leagueGroups()->with('leagueTeams.team')->get();
        $fixtures = $league->fixtures()
            ->with(['homeTeam.team', 'awayTeam.team', 'leagueGroup'])
            ->orderByRaw("CASE match_type WHEN 'group_stage' THEN 0 WHEN 'qualifier' THEN 1 WHEN 'eliminator' THEN 2 WHEN 'quarter_final' THEN 3 WHEN 'semi_final' THEN 4 WHEN 'final' THEN 5 ELSE 6 END")
            ->orderBy('league_group_id')
            ->orderBy('sort_order')
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get();
        $grounds = Ground::orderBy('name')->get(['id', 'name']);

        return view('leagues.league-match.fixture-setup', compact('league', 'groups', 'fixtures', 'grounds'));
    }

    public function createGroups(CreateGroupsRequest $request, League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($request, $league) {
            $league->leagueGroups()->delete();

            foreach ($request->groups as $index => $groupData) {
                $group = $league->leagueGroups()->create([
                    'name' => $groupData['name'],
                    'sort_order' => $index
                ]);

                $group->leagueTeams()->attach($groupData['team_ids']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Groups created successfully!'
        ]);
    }

    public function generateFixtures(GenerateFixturesRequest $request, League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($request, $league) {
            $league->fixtures()->delete();

            $groups = $league->leagueGroups()->with('leagueTeams')->orderBy('sort_order')->orderBy('id')->get();
            $nextOrder = 1;

            foreach ($groups as $group) {
                $nextOrder = $this->generateGroupFixtures($group, $request->format, $nextOrder);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Fixtures generated successfully!'
        ]);
    }

    private function generateGroupFixtures(LeagueGroup $group, string $format, int $startingOrder = 1): int
    {
        $teams = $group->leagueTeams->pluck('id')->toArray();
        $teamCount = count($teams);
        $fixtures = [];

        if ($teamCount < 2) {
            return $startingOrder; // Need at least 2 teams
        }

        $order = 1;

        // Single round-robin: each team plays every other team once
        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $fixtures[] = [
                    'league_id' => $group->league_id,
                    'league_group_id' => $group->id,
                    'home_team_id' => $teams[$i],
                    'away_team_id' => $teams[$j],
                    'match_type' => 'group_stage',
                    'status' => 'unscheduled',
                    'sort_order' => $startingOrder + ($order - 1),
                ];

                // Double round-robin: add return fixture
                if ($format === 'double_round_robin') {
                    $fixtures[] = [
                        'league_id' => $group->league_id,
                        'league_group_id' => $group->id,
                        'home_team_id' => $teams[$j],
                        'away_team_id' => $teams[$i],
                        'match_type' => 'group_stage',
                        'status' => 'unscheduled',
                        'sort_order' => $startingOrder + ($order - 1),
                    ];
                }

                $order++;
            }
        }

        // Add slug and timestamps
        foreach ($fixtures as &$fixture) {
            $fixture['slug'] = Str::slug($group->league->slug . '-fixture-' . uniqid());
            $fixture['created_at'] = now();
            $fixture['updated_at'] = now();
        }

        if (!empty($fixtures)) {
            Fixture::insert($fixtures);
        }

        return $startingOrder + count($fixtures);
    }

    public function fixtures(League $league)
    {
        $fixtures = $league->fixtures()
            ->with([
                'homeTeam.team',
                'awayTeam.team',
                'leagueGroup'
            ])
            ->orderByRaw("CASE match_type WHEN 'group_stage' THEN 0 WHEN 'qualifier' THEN 1 WHEN 'eliminator' THEN 2 WHEN 'quarter_final' THEN 3 WHEN 'semi_final' THEN 4 WHEN 'final' THEN 5 ELSE 6 END")
            ->orderBy('league_group_id')
            ->orderBy('sort_order')
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($fixture) {
                return $fixture->leagueGroup?->name ?? 'Knockout / Unassigned';
            });

        $retentionByTeam = LeaguePlayer::where('league_id', $league->id)
            ->where(function ($q) {
                $q->where('retention', true)
                    ->orWhere('status', 'retained');
            })
            ->with(['player.position'])
            ->get()
            ->groupBy('league_team_id');

        $soldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'sold')
            ->with(['player.position', 'leagueTeam.team'])
            ->orderByDesc('bid_price')
            ->get();

        $topBoughtOverall = $soldPlayers->take(6);
        $topBoughtByTeam = $soldPlayers->groupBy('league_team_id')->map(function ($players) {
            return $players->take(2);
        });

        return view('leagues.fixtures.index', compact('league', 'fixtures', 'retentionByTeam', 'topBoughtByTeam', 'topBoughtOverall'));
    }

    public function createFixture(Request $request, League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'match_type' => 'required|in:group_stage,qualifier,eliminator,quarter_final,semi_final,final',
            'league_group_id' => 'nullable|exists:league_groups,id',
            'home_team_id' => 'required|exists:league_teams,id',
            'away_team_id' => 'required|exists:league_teams,id|different:home_team_id',
            'match_date' => 'nullable|date',
            'match_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255'
        ]);

        $status = ($request->match_date && $request->match_time) ? 'scheduled' : 'unscheduled';
        $nextOrder = ($league->fixtures()->max('sort_order') ?? 0) + 1;

        $fixture = $league->fixtures()->create([
            'slug' => Str::slug($league->slug . '-fixture-' . uniqid()),
            'match_type' => $request->match_type,
            'league_group_id' => $request->league_group_id,
            'home_team_id' => $request->home_team_id,
            'away_team_id' => $request->away_team_id,
            'match_date' => $request->match_date,
            'match_time' => $request->match_time,
            'venue' => $request->venue,
            'status' => $status,
            'sort_order' => $nextOrder,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fixture created successfully!',
            'fixture' => $fixture
        ]);
    }

    public function updateFixture(Request $request, League $league, Fixture $fixture)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'match_date' => 'nullable|date',
            'match_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'status' => 'nullable|in:unscheduled,scheduled,in_progress,completed,cancelled',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $fixture->update($validated);

        // Update status to scheduled if date and time are set
        if ($fixture->match_date && $fixture->match_time && ($fixture->status === 'unscheduled' || !$fixture->status)) {
            $fixture->update(['status' => 'scheduled']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fixture updated successfully!'
        ]);
    }

    public function reorderFixtures(Request $request, League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.fixture' => 'required|string',
            'orders.*.sort_order' => 'required|integer|min:1',
        ]);

        $orders = collect($validated['orders']);
        $fixtures = $league->fixtures()
            ->whereIn('slug', $orders->pluck('fixture'))
            ->get()
            ->keyBy('slug');

        DB::transaction(function () use ($orders, $fixtures) {
            $orders->each(function ($item) use ($fixtures) {
                if (isset($fixtures[$item['fixture']])) {
                    $fixtures[$item['fixture']]->update([
                        'sort_order' => $item['sort_order'],
                    ]);
                }
            });

            // Re-sequence kickoff slots to follow the new order while preserving the original set of times
            $slots = $fixtures->values()->sortBy(function (Fixture $fixture) {
                $date = $fixture->match_date ? $fixture->match_date->toDateString() : '9999-12-31';
                $time = $fixture->match_time ? $fixture->match_time->format('H:i') : '23:59';
                return $date . ' ' . $time;
            })->map(function (Fixture $fixture) {
                return [
                    'match_date' => $fixture->match_date ? $fixture->match_date->toDateString() : null,
                    'match_time' => $fixture->match_time ? $fixture->match_time->format('H:i') : null,
                ];
            })->values();

            $orderedBySort = $orders->sortBy('sort_order')->values();

            $orderedBySort->each(function ($item, $index) use ($fixtures, $slots) {
                $fixture = $fixtures[$item['fixture']] ?? null;
                $slot = $slots[$index] ?? ['match_date' => null, 'match_time' => null];
                if (!$fixture) {
                    return;
                }

                $payload = [
                    'match_date' => $slot['match_date'],
                    'match_time' => $slot['match_time'],
                ];

                if ($slot['match_date'] && $slot['match_time']) {
                    $payload['status'] = 'scheduled';
                }

                $fixture->update($payload);
            });
        });

        return response()->json([
            'success' => true,
            'message' => 'Fixture order updated',
        ]);
    }

    public function regenerateWithSchedule(Request $request, League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'format' => 'required|in:single_round,double_round',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'matches_per_day' => 'required|integer|min:1',
            'match_duration' => 'required|integer|min:10',
            'gap_minutes' => 'required|integer|min:0',
            'days' => 'required|integer|min:1',
        ]);

        $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $format = $validated['format'] === 'double_round' ? 'double_round_robin' : 'single_round_robin';
        $matchesPerDay = (int) $validated['matches_per_day'];
        $matchDuration = (int) $validated['match_duration'];
        $gapMinutes = (int) $validated['gap_minutes'];

        DB::transaction(function () use ($league, $format, $startDateTime, $matchesPerDay, $matchDuration, $gapMinutes) {
            $league->fixtures()->where('match_type', 'group_stage')->delete();

            $groups = $league->leagueGroups()->with('leagueTeams')->orderBy('sort_order')->orderBy('id')->get();
            $nextOrder = 1;

            foreach ($groups as $group) {
                $nextOrder = $this->generateGroupFixtures($group, $format, $nextOrder);
            }

            $this->applySchedule($league, $startDateTime, $matchesPerDay, $matchDuration, $gapMinutes);
        });

        $totalFixtures = $league->fixtures()->where('match_type', 'group_stage')->count();
        $capacity = $validated['matches_per_day'] * $validated['days'];

        $message = $capacity < $totalFixtures
            ? 'Fixtures scheduled across more days than requested based on total matches.'
            : 'Fixtures generated and scheduled.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'scheduled' => $totalFixtures,
        ]);
    }

    public function shuffleFixtures(Request $request, League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($league) {
            $fixturesByGroup = $league->fixtures()
                ->where('match_type', 'group_stage')
                ->get()
                ->groupBy('league_group_id');

            $order = 1;

            foreach ($fixturesByGroup as $fixtures) {
                $slots = $this->collectSlots($fixtures);
                $shuffled = $this->buildNonRepeatingOrder($fixtures);
                $shuffled->each(function (Fixture $fixture, $index) use (&$order, $slots) {
                    $slot = $slots[$index] ?? ['match_date' => null, 'match_time' => null];
                    $payload = [
                        'sort_order' => $order++,
                        'match_date' => $slot['match_date'],
                        'match_time' => $slot['match_time'],
                    ];
                    if ($slot['match_date'] && $slot['match_time']) {
                        $payload['status'] = 'scheduled';
                    }
                    $fixture->update($payload);
                });
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Group fixtures shuffled',
        ]);
    }

    private function applySchedule(League $league, Carbon $startDateTime, int $matchesPerDay, int $matchDurationMinutes, int $gapMinutes): void
    {
        $fixtures = $league->fixtures()
            ->where('match_type', 'group_stage')
            ->orderBy('sort_order')
            ->get();

        $fixtures->each(function (Fixture $fixture, int $index) use ($startDateTime, $matchesPerDay, $matchDurationMinutes, $gapMinutes) {
            $dayOffset = intdiv($index, $matchesPerDay);
            $slotInDay = $index % $matchesPerDay;

            $kickoff = (clone $startDateTime)
                ->addDays($dayOffset)
                ->addMinutes(($matchDurationMinutes + $gapMinutes) * $slotInDay);

            $fixture->update([
                'match_date' => $kickoff->toDateString(),
                'match_time' => $kickoff->format('H:i'),
                'status' => 'scheduled',
            ]);
        });
    }

    private function collectSlots(Collection $fixtures): Collection
    {
        return $fixtures
            ->sortBy('sort_order')
            ->values()
            ->map(function (Fixture $fixture) {
                return [
                    'match_date' => $fixture->match_date ? $fixture->match_date->toDateString() : null,
                    'match_time' => $fixture->match_time ? $fixture->match_time->format('H:i') : null,
                ];
            });
    }

    private function buildNonRepeatingOrder(Collection $fixtures, int $attempts = 50): Collection
    {
        $fixtures = $fixtures->values();

        for ($i = 0; $i < $attempts; $i++) {
            $shuffled = $fixtures->shuffle()->values();
            if ($this->isNonRepeating($shuffled)) {
                return $shuffled;
            }
        }

        return $fixtures->shuffle()->values();
    }

    private function isNonRepeating(Collection $ordered): bool
    {
        $prevTeams = null;
        foreach ($ordered as $fixture) {
            /** @var Fixture $fixture */
            $currentTeams = [
                $fixture->home_team_id,
                $fixture->away_team_id,
            ];

            if ($prevTeams && (in_array($currentTeams[0], $prevTeams, true) || in_array($currentTeams[1], $prevTeams, true))) {
                return false;
            }

            $prevTeams = $currentTeams;
        }

        return true;
    }

    public function exportPdf(League $league)
    {
        if (!auth()->user()->isOrganizerForLeague($league->id) && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to league');
        }

        $groups = $league->leagueGroups()->with('leagueTeams.team')->get();
        $fixtures = $league->fixtures()
            ->with(['homeTeam.team', 'awayTeam.team', 'leagueGroup'])
            ->orderBy('match_type')
            ->orderBy('league_group_id')
            ->orderBy('match_date')
            ->get();

        $pdf = \PDF::loadView('leagues.fixtures.pdf', compact('league', 'groups', 'fixtures'));
        
        return $pdf->download($league->name . '_fixtures.pdf');
    }
}
