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
        // Ideally we should scope this to the logged in user's assigned leagues or matches
        // For now, let's assume admins/scorers can see all or we filter by their assignment if implemented
        $user = auth()->user();

        $fixtures = Fixture::query()
            ->with(['homeTeam.team', 'awayTeam.team', 'league', 'leagueGroup'])
            ->orderByRaw("FIELD(status, 'in_progress', 'scheduled', 'unscheduled', 'completed', 'cancelled')")
            ->orderBy('match_date')
            ->orderBy('match_time');

        if (!$user->isAdmin()){
             // If not admin, maybe filter by scorer_id if that exists, or just show all for now as per "Scorer Dashboard" request
             // hinting at a general management view.
             // If we want to be strict: $fixtures->where('scorer_id', $user->id);
        }

        $fixtures = $fixtures->get();

        return view('scorer.dashboard', compact('fixtures'));
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
