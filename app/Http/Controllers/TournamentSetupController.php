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

class TournamentSetupController extends Controller
{
    public function index(League $league)
    {
        if ($league->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to league');
        }

        if ($league->status !== 'auction_completed') {
            return redirect()->route('leagues.show', $league)
                ->with('error', 'Tournament setup is only available after auction completion.');
        }

        $leagueTeams = $league->leagueTeams()->with('team')->get();
        $existingGroups = $league->leagueGroups()->with('leagueTeams.team')->get();

        return view('leagues.tournament-setup.index', compact('league', 'leagueTeams', 'existingGroups'));
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
        $teams = $group->leagueTeams->toArray();
        $fixtures = [];

        for ($i = 0; $i < count($teams); $i++) {
            for ($j = $i + 1; $j < count($teams); $j++) {
                $fixtures[] = [
                    'league_id' => $group->league_id,
                    'league_group_id' => $group->id,
                    'home_team_id' => $teams[$i]['id'],
                    'away_team_id' => $teams[$j]['id'],
                    'match_type' => 'group_stage',
                    'status' => 'unscheduled'
                ];

                if ($format === 'double_round_robin') {
                    $fixtures[] = [
                        'league_id' => $group->league_id,
                        'league_group_id' => $group->id,
                        'home_team_id' => $teams[$j]['id'],
                        'away_team_id' => $teams[$i]['id'],
                        'match_type' => 'group_stage',
                        'status' => 'unscheduled'
                    ];
                }
            }
        }

        foreach ($fixtures as &$fixture) {
            $fixture['slug'] = Str::slug($group->league->slug . '-fixture-' . uniqid());
            $fixture['created_at'] = now();
            $fixture['updated_at'] = now();
        }

        Fixture::insert($fixtures);
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
}