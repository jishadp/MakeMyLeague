<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyLeaguesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get leagues where user is organizer
        $organizedLeagues = $user->isOrganizer() ? 
            \App\Models\League::where('user_id', $user->id)->with(['game', 'leagueTeams.team'])->get() : 
            collect();
        
        // Get leagues where user is a player (approved/sold)
        $playingLeagues = $user->leaguePlayers()->whereIn('status', ['approved', 'sold', 'active'])->with(['league.game', 'leagueTeam.team'])->get()->pluck('league')->unique('id');
        
        // Get leagues where user has requested to join (pending)
        $requestedLeagues = $user->leaguePlayers()->where('status', 'pending')->with(['league.game', 'leagueTeam.team'])->get()->pluck('league')->unique('id');
        
        // Get leagues where user owns a team
        $teamOwnerLeagues = $user->isOwner() ? 
            \App\Models\League::whereHas('leagueTeams.team', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->with(['game', 'leagueTeams.team'])->get() : 
            collect();
        
        return view('my-leagues.index', compact('organizedLeagues', 'playingLeagues', 'requestedLeagues', 'teamOwnerLeagues'));
    }
}