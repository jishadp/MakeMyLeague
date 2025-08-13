<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaguePlayerController extends Controller
{
    /**
     * Display a listing of league players.
     */
    public function index(League $league): View
    {
        $leaguePlayers = LeaguePlayer::with(['leagueTeam.team', 'user', 'user.role'])
            ->whereHas('leagueTeam', function($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('retention'), function($query, $retention) {
                $query->where('retention', $retention === 'true');
            })
            ->when(request('team'), function($query, $teamId) {
                $query->whereHas('leagueTeam', function($subQuery) use ($teamId) {
                    $subQuery->where('team_id', $teamId);
                });
            })
            ->orderBy('base_price', 'desc')
            ->paginate(15);

        // Get available teams for filtering
        $teams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get()
            ->pluck('team');

        // Get status counts
        $statusCounts = LeaguePlayer::whereHas('leagueTeam', function($query) use ($league) {
            $query->where('league_id', $league->id);
        })
        ->selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

        return view('league-players.index', compact('league', 'leaguePlayers', 'teams', 'statusCounts'));
    }

    /**
     * Show the form for creating a new league player.
     */
    public function create(League $league): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();

        // Get players not already in this league
        $availablePlayers = User::whereNotNull('role_id')
            ->whereNotIn('id', function($query) use ($league) {
                $query->select('user_id')
                      ->from('league_players')
                      ->join('league_teams', 'league_players.league_team_id', '=', 'league_teams.id')
                      ->where('league_teams.league_id', $league->id);
            })
            ->with('role')
            ->get();

        return view('league-players.create', compact('league', 'leagueTeams', 'availablePlayers'));
    }

    /**
     * Store a newly created league player.
     */
    public function store(Request $request, League $league)
    {
        $request->validate([
            'league_team_id' => 'required|exists:league_teams,id',
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $league) {
                    $exists = LeaguePlayer::whereHas('leagueTeam', function($query) use ($league) {
                        $query->where('league_id', $league->id);
                    })->where('user_id', $value)->exists();
                    
                    if ($exists) {
                        $fail('This player is already registered in this league.');
                    }
                },
            ],
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
            'base_price' => 'required|numeric|min:0',
        ]);

        // Validate league team belongs to current league
        $leagueTeam = LeagueTeam::findOrFail($request->league_team_id);
        if ($leagueTeam->league_id !== $league->id) {
            return back()->withErrors(['league_team_id' => 'Invalid team selected.']);
        }

        LeaguePlayer::create($request->all());

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', 'Player added to league successfully!');
    }

    /**
     * Display the specified league player.
     */
    public function show(League $league, LeaguePlayer $leaguePlayer): View
    {
        $leaguePlayer->load(['leagueTeam.team', 'user', 'user.role']);

        return view('league-players.show', compact('league', 'leaguePlayer'));
    }

    /**
     * Show the form for editing the specified league player.
     */
    public function edit(League $league, LeaguePlayer $leaguePlayer): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();

        return view('league-players.edit', compact('league', 'leaguePlayer', 'leagueTeams'));
    }

    /**
     * Update the specified league player.
     */
    public function update(Request $request, League $league, LeaguePlayer $leaguePlayer)
    {
        $request->validate([
            'league_team_id' => 'required|exists:league_teams,id',
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
            'base_price' => 'required|numeric|min:0',
        ]);

        // Validate league team belongs to current league
        $leagueTeam = LeagueTeam::findOrFail($request->league_team_id);
        if ($leagueTeam->league_id !== $league->id) {
            return back()->withErrors(['league_team_id' => 'Invalid team selected.']);
        }

        $leaguePlayer->update($request->all());

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', 'League player updated successfully!');
    }

    /**
     * Remove the specified league player.
     */
    public function destroy(League $league, LeaguePlayer $leaguePlayer)
    {
        $leaguePlayer->delete();

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', 'Player removed from league successfully!');
    }

    /**
     * Update player status.
     */
    public function updateStatus(Request $request, League $league, LeaguePlayer $leaguePlayer)
    {
        $request->validate([
            'status' => 'required|in:pending,available,sold,unsold,skip',
        ]);

        $leaguePlayer->update(['status' => $request->status]);

        return back()->with('success', 'Player status updated successfully!');
    }

    /**
     * Bulk update player statuses.
     */
    public function bulkUpdateStatus(Request $request, League $league)
    {
        $request->validate([
            'player_ids' => 'required|array',
            'player_ids.*' => 'exists:league_players,id',
            'status' => 'required|in:pending,available,sold,unsold,skip',
        ]);

        LeaguePlayer::whereIn('id', $request->player_ids)
            ->whereHas('leagueTeam', function($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->update(['status' => $request->status]);

        $count = count($request->player_ids);
        return back()->with('success', "{$count} players' status updated successfully!");
    }
}
