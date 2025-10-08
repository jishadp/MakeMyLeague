<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyLeaguesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get leagues where user is organizer (both approved and pending)
        $organizedLeagues = $user->organizedLeagues()->with(['game', 'leagueTeams.team', 'organizers'])->get();
        
        // Get leagues where user is a player (sold/available)
        $playingLeagues = $user->leaguePlayers()->whereIn('status', ['sold', 'available'])->with(['league.game', 'leagueTeam.team'])->get()->pluck('league')->unique('id');
        
        // Get leagues where user has requested to join (pending)
        $requestedLeagues = $user->leaguePlayers()->where('status', 'pending')->with(['league.game', 'leagueTeam.team'])->get()->pluck('league')->unique('id');
        
        // Get leagues where user owns a team
        $teamOwnerLeagues = $user->isTeamOwner() ? 
            \App\Models\League::whereHas('leagueTeams.team', function($query) use ($user) {
                $query->whereHas('owners', function($q) use ($user) {
                    $q->where('user_id', $user->id)->where('role', 'owner');
                });
            })->with(['game', 'leagueTeams.team', 'leagueTeams.auctioneer'])->get() : 
            collect();
        
        return view('my-leagues.index', compact('organizedLeagues', 'playingLeagues', 'requestedLeagues', 'teamOwnerLeagues'));
    }
}