<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTeamsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get teams owned by user using the new team_owners relationship
        $ownedTeams = $user->primaryOwnedTeams()->with([
            'homeGround', 
            'localBody', 
            'leagueTeams.league' => function($query) {
                $query->with(['game']);
            }
        ])->get();
        
        // Get teams where user is a player (sold/available)
        $playerTeams = $user->leaguePlayers()->whereIn('status', ['sold', 'available'])
            ->with(['leagueTeam.team.homeGround', 'leagueTeam.team.localBody', 'leagueTeam.league'])
            ->get();
        
        // Get teams where user has requested to join (pending)
        $requestedTeams = $user->leaguePlayers()->where('status', ['pending'])
            ->with(['leagueTeam.team.homeGround', 'leagueTeam.team.localBody', 'leagueTeam.league'])
            ->get();
        
        return view('my-teams.index', compact('ownedTeams', 'playerTeams', 'requestedTeams'));
    }
}