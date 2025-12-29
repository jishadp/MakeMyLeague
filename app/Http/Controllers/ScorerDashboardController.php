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
        if ($user->isAdmin()) {
            $leagues = League::where('status', 'active')->get();
        } else {
            // Get leagues where user is an organizer (approved)
            $leagues = $user->approvedOrganizedLeagues;
            
            // Should we also include leagues where they are just a scorer? 
            // The request says "only list leagues as user access", primarily hinting at management.
            // If they are just a scorer for a specific match, that's trickier without a "Scorer" role on the league itself.
            // For now, adhering to "user who access can select the league" which implies management rights.
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
            // Fallback: If no leagues available to manage, maybe show nothing or just their assigned matches?
            // "Show only the league based" - if no league selected, show nothing safe default.
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
        // We need a league context to create a match. 
        // Either the user selects a league first, or we pass it in.
        // For a general "Create Match" button, we might need to select the league.
        
        $leagues = League::where('status', 'active')->get();
        
        return view('scorer.matches.create', compact('leagues'));
    }

    public function getLeagueTeams(League $league)
    {
        return response()->json([
            'teams' => $league->leagueTeams()->with('team')->get(),
            'groups' => $league->leagueGroups()->with('leagueTeams')->get()
        ]);
    }

    public function storeMatch(Request $request)
    {
        $validated = $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'match_type' => 'required|string', // group_stage, qualifier, etc.
            'league_group_id' => 'nullable|exists:league_groups,id',
            'home_team_id' => 'required|exists:league_teams,id',
            'away_team_id' => 'required|exists:league_teams,id|different:home_team_id',
            'match_date' => 'nullable|date',
            'match_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string'
        ]);

        $league = League::findOrFail($validated['league_id']);
        
        // Status determination
        $status = ($validated['match_date'] && $validated['match_time']) ? 'scheduled' : 'unscheduled';
        
        $fixture = Fixture::create([
            'slug' => Str::slug($league->slug . '-fixture-' . uniqid()),
            'league_id' => $league->id,
            'match_type' => $validated['match_type'],
            'league_group_id' => $validated['league_group_id'] ?? null,
            'home_team_id' => $validated['home_team_id'],
            'away_team_id' => $validated['away_team_id'],
            'match_date' => $validated['match_date'],
            'match_time' => $validated['match_time'],
            'venue' => $validated['venue'],
            'status' => $status,
            'scorer_id' => auth()->id() // Auto-assign creator as scorer? Or leave null.
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Match created successfully',
            'redirect' => route('scorer.dashboard')
        ]);
    }
}
