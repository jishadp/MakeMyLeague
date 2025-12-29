<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\League;
use App\Models\LeagueGroup;
use App\Models\LeagueTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScorerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $leagues = collect();
        $selectedLeagueId = $request->input('league_id');

        // 1. Fetch relevant leagues for the dropdown
        // Show leagues that are Active or where Auction is Completed
        $allowedStatuses = ['active', 'auction_completed'];

        if ($user->isAdmin()) {
            $leagues = League::whereIn('status', $allowedStatuses)->get();
        } else {
            // Get leagues where user is an organizer (approved) and status is valid
            $leagues = $user->approvedOrganizedLeagues()
                ->whereIn('status', $allowedStatuses)
                ->get();
        }

        // 2. Determine Selected League
        $selectedLeague = null;
        if ($selectedLeagueId) {
            $selectedLeague = $leagues->firstWhere('id', $selectedLeagueId);
        }

        // If no valid selection (or first load), default to first available
        if (!$selectedLeague && $leagues->isNotEmpty()) {
            $selectedLeague = $leagues->first();
        }

        // 3. Query Fixtures
        $fixtures = Fixture::query()
            ->with(['homeTeam.team', 'awayTeam.team', 'league', 'leagueGroup']);

        if ($selectedLeague) {
            $fixtures->where('league_id', $selectedLeague->id);
        } else {
            // Fallback: If no leagues available, show nothing
            $fixtures->whereRaw('0 = 1'); 
        }

        $fixtures = $fixtures->orderByRaw("FIELD(status, 'in_progress', 'scheduled', 'unscheduled', 'completed', 'cancelled')")
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get();

        return view('scorer.dashboard', compact('fixtures', 'leagues', 'selectedLeague'));
    }

    public function createMatch(Request $request) 
    {
        $user = auth()->user();
        $leagues = collect();

        // Show leagues that are Active or where Auction is Completed
        $allowedStatuses = ['active', 'auction_completed'];

        // Fetch user's leagues (same logic as index)
        if ($user->isAdmin()) {
            $leagues = League::whereIn('status', $allowedStatuses)->get();
        } else {
            $leagues = $user->approvedOrganizedLeagues()
                ->whereIn('status', $allowedStatuses)
                ->get();
        }

        // Resolve selected league from slug
        $leagueSlug = $request->query('league');
        $selectedLeague = null;

        if ($leagueSlug) {
            $selectedLeague = $leagues->firstWhere('slug', $leagueSlug);
            
            // If explicit slug provided but not found in user's list (e.g. admin accessing via slug but not loaded all?), 
            // fallback to finding it directly if admin, or error if unauthorized.
            if (!$selectedLeague && $user->isAdmin()) {
                 $selectedLeague = League::where('slug', $leagueSlug)->first();
            }
        }

        // If still no selected league, and user has leagues, maybe default? 
        // Better to let them select in the UI if strictly needed, but the Wizard usually starts with context.
        
        return view('scorer.matches.create', compact('leagues', 'selectedLeague'));
    }

    public function getLeagueTeams(League $league)
    {
        // Eager load teams with their relationships
        // Use 'team' relation to get names etc.
        $teams = $league->leagueTeams()->with(['team'])->get();
        
        // Load groups with their teams
        $groups = $league->leagueGroups()->with(['leagueTeams.team'])->get();

        // Transform groups to include league_teams in the response
        $groupsData = $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'league_teams' => $group->leagueTeams->map(function ($leagueTeam) {
                    return [
                        'id' => $leagueTeam->id,
                        'team_id' => $leagueTeam->team_id,
                        'league_id' => $leagueTeam->league_id,
                        // Include the nested team object for name display
                        'team' => $leagueTeam->team 
                    ];
                })->values()
            ];
        });

        return response()->json([
            'teams' => $teams,
            'groups' => $groupsData
        ]);
    }

    public function storeMatch(Request $request)
    {
        try {
            $validated = $request->validate([
                'league_id' => 'required|exists:leagues,id',
                'match_type' => 'required|string',
                'league_group_id' => 'nullable|exists:league_groups,id',
                'home_team_id' => 'required|exists:league_teams,id',
                'away_team_id' => 'required|exists:league_teams,id|different:home_team_id',
                'match_date' => 'nullable|date',
                'match_time' => 'nullable', 
                'venue' => 'nullable|string'
            ]);

            $league = League::findOrFail($validated['league_id']);
            
            // Ensure league_group_id is only set for group_stage matches
            if ($validated['match_type'] !== 'group_stage') {
                $validated['league_group_id'] = null;
            }
            
            // Validate that if it's group_stage, league_group_id is provided
            if ($validated['match_type'] === 'group_stage' && empty($validated['league_group_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Group is required for group stage matches.'
                ], 422);
            }
            
            // Status determination
            $status = ($validated['match_date'] && $validated['match_time']) ? 'scheduled' : 'unscheduled';
            
            $fixture = Fixture::create([
                'league_id' => $league->id,
                'match_type' => $validated['match_type'],
                'league_group_id' => $validated['league_group_id'] ?? null,
                'home_team_id' => $validated['home_team_id'],
                'away_team_id' => $validated['away_team_id'],
                'match_date' => $validated['match_date'],
                'match_time' => $validated['match_time'],
                'venue' => $validated['venue'],
                'status' => $status,
                'scorer_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Match created successfully',
                'redirect' => route('scorer.dashboard', ['league_id' => $league->id])
            ]);
        } catch (\Exception $e) {
            \Log::error('Match Creation Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating match: ' . $e->getMessage()
            ], 500);
        }
    }
}
