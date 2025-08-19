<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeagueTeamController extends Controller
{
    /**
     * Display a listing of league teams.
     */
    public function index(League $league): View
    {
        $leagueTeams = LeagueTeam::with(['team', 'team.owner', 'team.homeGround'])
            ->where('league_id', $league->id)
            ->paginate(10);

        return view('league-teams.index', compact('league', 'leagueTeams'));
    }

    /**
     * Show the form for creating a new league team.
     */
    public function create(League $league): View
    {
        // Get teams that are not already in this league
        $availableTeams = Team::whereNotIn('id', function($query) use ($league) {
            $query->select('team_id')
                  ->from('league_teams')
                  ->where('league_id', $league->id);
        })->get();

        return view('league-teams.create', compact('league', 'availableTeams'));
    }

    /**
     * Store a newly created league team.
     */
    public function store(Request $request, League $league)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id|unique:league_teams,team_id,NULL,id,league_id,' . $league->id,
            'status' => 'required|in:pending,available',
            'wallet_balance' => 'nullable|numeric|min:0|max:' . $league->team_wallet_limit,
        ]);

        LeagueTeam::create([
            'league_id' => $league->id,
            'team_id' => $request->team_id,
            'status' => $request->status,
            'wallet_balance' => $request->wallet_balance ?? $league->team_wallet_limit,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('league-teams.index', $league)
            ->with('success', 'Team added to league successfully!');
    }

    /**
     * Display the specified league team.
     */
    public function show(League $league, LeagueTeam $leagueTeam): View
    {
        $leagueTeam->load(['team', 'team.owner', 'team.homeGround', 'players.user']);

        // Get other league players available for auction
        $otherLeaguePlayers = \App\Models\LeaguePlayer::with(['user', 'user.position'])
            ->whereNull('league_team_id')
            ->where('status', 'available')
            ->get();

        return view('league-teams.show', compact('league', 'leagueTeam', 'otherLeaguePlayers'));
    }

    /**
     * Show the form for editing the specified league team.
     */
    public function edit(League $league, LeagueTeam $leagueTeam): View
    {
        return view('league-teams.edit', compact('league', 'leagueTeam'));
    }

    /**
     * Update the specified league team.
     */
    public function update(Request $request, League $league, LeagueTeam $leagueTeam)
    {
        $request->validate([
            'status' => 'required|in:pending,available',
            'wallet_balance' => 'nullable|numeric|min:0|max:' . $league->team_wallet_limit,
        ]);

        $leagueTeam->update([
            'status' => $request->status,
            'wallet_balance' => $request->wallet_balance ?? $leagueTeam->wallet_balance,
        ]);

        return redirect()
            ->route('league-teams.index', $league)
            ->with('success', 'League team updated successfully!');
    }

    /**
     * Remove the specified league team.
     */
    public function destroy(League $league, LeagueTeam $leagueTeam)
    {
        $leagueTeam->delete();

        return redirect()
            ->route('league-teams.index', $league)
            ->with('success', 'Team removed from league successfully!');
    }

    /**
     * Update team status.
     */
    public function updateStatus(Request $request, League $league, LeagueTeam $leagueTeam)
    {
        $request->validate([
            'status' => 'required|in:pending,available',
        ]);

        $leagueTeam->update(['status' => $request->status]);

        return back()->with('success', 'Team status updated successfully!');
    }

    /**
     * Update wallet balance.
     */
    public function updateWallet(Request $request, League $league, LeagueTeam $leagueTeam)
    {
        $request->validate([
            'wallet_balance' => 'required|numeric|min:0|max:' . $league->team_wallet_limit,
        ]);

        $leagueTeam->update(['wallet_balance' => $request->wallet_balance]);

        return back()->with('success', 'Wallet balance updated successfully!');
    }
}
