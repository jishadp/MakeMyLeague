<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyLeaguesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // If user is admin, show all leagues
        if ($user->isAdmin()) {
            $organizedLeagues = \App\Models\League::with(['game', 'leagueTeams.team', 'organizers', 'winnerTeam.team', 'runnerTeam.team'])->get();
        } else {
            // Get leagues where user is organizer (both approved and pending)
            $organizedLeagues = $user->organizedLeagues()->with(['game', 'leagueTeams.team', 'organizers', 'winnerTeam.team', 'runnerTeam.team'])->get();
        }
        
        // Get leagues where user is a player (sold/available)
        $playingLeagues = $user->leaguePlayers()->whereIn('status', ['sold', 'available'])->with(['league.game', 'league.winnerTeam.team', 'league.runnerTeam.team', 'leagueTeam.team'])->get()->pluck('league')->unique('id')->values();
        
        // Get leagues where user has requested to join (pending)
        $requestedLeagues = $user->leaguePlayers()->where('status', 'pending')->with(['league.game', 'league.winnerTeam.team', 'league.runnerTeam.team', 'leagueTeam.team'])->get()->pluck('league')->unique('id')->values();
        
        // Get leagues where user owns a team
        $teamOwnerLeagues = $user->isTeamOwner() ? 
            \App\Models\League::whereHas('leagueTeams.team', function($query) use ($user) {
                $query->whereHas('owners', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('role', 'owner');
                });
            })->with([
                'game', 
                'leagueTeams' => function($query) use ($user) {
                    $query->whereHas('team', function($q) use ($user) {
                        $q->whereHas('owners', function($ownerQuery) use ($user) {
                            $ownerQuery->where('user_id', $user->id)->where('role', 'owner');
                        });
                    });
                },
                'leagueTeams.team', 
                'leagueTeams.auctioneer',
                'winnerTeam.team',
                'runnerTeam.team'
            ])->get() : 
            collect();
        
        // Get leagues where user is assigned as an auctioneer
        $auctioneerLeagues = \App\Models\League::whereHas('leagueTeams.teamAuctioneer', function($query) use ($user) {
            $query->where('auctioneer_id', $user->id)->where('status', 'active');
        })->with([
            'game',
            'leagueTeams' => function($query) use ($user) {
                $query->whereHas('teamAuctioneer', function($q) use ($user) {
                    $q->where('auctioneer_id', $user->id)->where('status', 'active');
                })->with(['team', 'teamAuctioneer']);
            },
            'winnerTeam.team',
            'runnerTeam.team'
        ])->get();
        
        return response()->json([
            'organized_leagues' => $organizedLeagues,
            'playing_leagues' => $playingLeagues,
            'requested_leagues' => $requestedLeagues,
            'team_owner_leagues' => $teamOwnerLeagues,
            'auctioneer_leagues' => $auctioneerLeagues,
        ]);
    }
}
