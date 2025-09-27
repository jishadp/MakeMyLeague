<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupsRequest;
use App\Http\Requests\GenerateFixturesRequest;
use App\Models\League;
use App\Models\LeagueGroup;
use App\Models\Fixture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeagueMatchController extends Controller
{
    public function index(League $league)
    {
        if ($league->user_id !== auth()->id()) {
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
        if ($league->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to league');
        }

        $groups = $league->leagueGroups()->with('leagueTeams.team')->get();
        $fixtures = $league->fixtures()->with(['homeTeam.team', 'awayTeam.team', 'leagueGroup'])->get();

        return view('leagues.league-match.fixture-setup', compact('league', 'groups', 'fixtures'));
    }

    public function createGroups(CreateGroupsRequest $request, League $league)
    {
        if ($league->user_id !== auth()->id()) {
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
        if ($league->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($request, $league) {
            $league->fixtures()->delete();

            $groups = $league->leagueGroups()->with('leagueTeams')->get();

            foreach ($groups as $group) {
                $this->generateGroupFixtures($group, $request->format);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Fixtures generated successfully!'
        ]);
    }

    private function generateGroupFixtures(LeagueGroup $group, string $format)
    {
        $teams = $group->leagueTeams->pluck('id')->toArray();
        $teamCount = count($teams);
        $fixtures = [];

        if ($teamCount < 2) {
            return; // Need at least 2 teams
        }

        // Single round-robin: each team plays every other team once
        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $fixtures[] = [
                    'league_id' => $group->league_id,
                    'league_group_id' => $group->id,
                    'home_team_id' => $teams[$i],
                    'away_team_id' => $teams[$j],
                    'match_type' => 'group_stage',
                    'status' => 'unscheduled'
                ];

                // Double round-robin: add return fixture
                if ($format === 'double_round_robin') {
                    $fixtures[] = [
                        'league_id' => $group->league_id,
                        'league_group_id' => $group->id,
                        'home_team_id' => $teams[$j],
                        'away_team_id' => $teams[$i],
                        'match_type' => 'group_stage',
                        'status' => 'unscheduled'
                    ];
                }
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
    }

    public function fixtures(League $league)
    {
        $fixtures = $league->fixtures()
            ->with([
                'homeTeam.team',
                'awayTeam.team',
                'leagueGroup'
            ])
            ->orderBy('league_group_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('leagueGroup.name');

        return view('leagues.fixtures.index', compact('league', 'fixtures'));
    }

    public function createFixture(Request $request, League $league)
    {
        if ($league->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'match_type' => 'required|in:group_stage,quarter_final,semi_final,final',
            'league_group_id' => 'nullable|exists:league_groups,id',
            'home_team_id' => 'required|exists:league_teams,id',
            'away_team_id' => 'required|exists:league_teams,id|different:home_team_id',
            'match_date' => 'nullable|date',
            'match_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255'
        ]);

        $status = ($request->match_date && $request->match_time) ? 'scheduled' : 'unscheduled';

        $fixture = $league->fixtures()->create([
            'slug' => Str::slug($league->slug . '-fixture-' . uniqid()),
            'match_type' => $request->match_type,
            'league_group_id' => $request->league_group_id,
            'home_team_id' => $request->home_team_id,
            'away_team_id' => $request->away_team_id,
            'match_date' => $request->match_date,
            'match_time' => $request->match_time,
            'venue' => $request->venue,
            'status' => $status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fixture created successfully!',
            'fixture' => $fixture
        ]);
    }

    public function updateFixture(Request $request, League $league, Fixture $fixture)
    {
        if ($league->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'match_date' => 'nullable|date',
            'match_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255'
        ]);

        $fixture->update($request->only(['match_date', 'match_time', 'venue']));

        // Update status to scheduled if date and time are set
        if ($fixture->match_date && $fixture->match_time && $fixture->status === 'unscheduled') {
            $fixture->update(['status' => 'scheduled']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fixture updated successfully!'
        ]);
    }

    public function exportPdf(League $league)
    {
        if ($league->user_id !== auth()->id()) {
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