<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'wallet_balance' => 'nullable|numeric|min:0|max:' . $league->team_wallet_limit,
        ]);
        $input = [
            'league_id' => $league->id,
            'team_id' => $request->team_id,
            'wallet_balance' => $request->wallet_balance ?? $league->team_wallet_limit,
            'created_by' => auth()->id(),
        ];
        if(auth()->user()->isOrganizer()){
            $input['status'] = $request->status;
        }
        

        LeagueTeam::create($input);

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

    /**
     * Display the manage teams page for a league.
     */
    public function manageTeams(League $league): View
    {
        $user = Auth::user();
        
        // Check if user is an organizer for this league, admin, or team owner in this league
        if (!$user->isOrganizerForLeague($league->id) && !$user->isAdmin() && !$user->hasAnyOwnedTeamInLeague($league->id)) {
            abort(403, 'You are not authorized to manage teams for this league.');
        }

        // If user is a team owner (not organizer/admin), only show their own teams
        if (!$user->isOrganizerForLeague($league->id) && !$user->isAdmin()) {
            $leagueTeams = $league->leagueTeams()->whereHas('team', function($query) use ($user) {
                $query->whereHas('owners', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('role', 'owner');
                });
            })->with([
                'team',
                'team.owners' => function($query) use ($user) {
                    $query->where('role', 'owner');
                },
                'auctioneer',
                'leaguePlayers' => function($query) {
                    $query->with('player.position');
                }
            ])->get();
            
            // Group teams by team for better organization when user has multiple teams
            $teamsByTeam = $leagueTeams->groupBy('team.id');
        } else {
            // Organizers and admins see all teams
            $leagueTeams = $league->leagueTeams()->with([
                'team',
                'team.owners' => function($query) {
                    $query->where('role', 'owner');
                },
                'auctioneer',
                'leaguePlayers' => function($query) {
                    $query->with('player.position');
                }
            ])->get();
            
            // Group teams by team for organizers/admins too
            $teamsByTeam = $leagueTeams->groupBy('team.id');
        }

        // Get league retention player limit
        $retentionLimit = $league->retention_players ?? 3; // Default to 3 if not set

        // Get available players for adding as retention (all players registered in league except sold players, who are not retention yet)
        $availableRetentionPlayers = \App\Models\LeaguePlayer::where('league_id', $league->id)
            ->where('status', '!=', 'sold')
            ->where('retention', false)
            ->with(['player.position', 'leagueTeam.team'])
            ->get();



        // Filter available players based on user permissions
        if (!$user->isOrganizerForLeague($league->id) && !$user->isAdmin()) {
            // Team owners can only add retention players to their own teams
            // Note: Available players might not have a league_team_id yet, so we allow them to be added to any team the user owns
            $userTeamIds = $leagueTeams->pluck('id'); // Use league_team IDs instead of team IDs
            $availableRetentionPlayers = $availableRetentionPlayers->filter(function($player) use ($userTeamIds) {
                // Allow players without a team (league_team_id is null) or players from user's teams
                return $player->league_team_id === null || $userTeamIds->contains($player->league_team_id);
            });
        }

        // Calculate statistics
        $totalTeams = $leagueTeams->count();
        $totalSoldPlayers = $leagueTeams->sum(function($team) {
            return $team->leaguePlayers->count();
        });
        $totalRetentionPlayers = $leagueTeams->sum(function($team) {
            return $team->leaguePlayers->where('retention', true)->count();
        });


        return view('league-teams.manage', compact('league', 'leagueTeams', 'teamsByTeam', 'retentionLimit', 'totalTeams', 'totalSoldPlayers', 'totalRetentionPlayers', 'availableRetentionPlayers'));
    }
}

