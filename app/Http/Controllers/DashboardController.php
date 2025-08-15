<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Models\GameRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController 
{
    public function view()
    {
        $leagues = League::with('game', 'organizer')->latest()->take(5)->get();
        $userLeagues = Auth::check() ? League::where('user_id', Auth::id())->get() : collect();
        $userOwnedTeams = Auth::check() ? Team::where('owner_id', Auth::id())->get() : collect();
        
        // Get player info if the current user has a role_id (is a player)
        $playerInfo = null;
        if (Auth::check() && Auth::user()->role_id) {
            $playerInfo = Auth::user()->load(['role', 'localBody']);
        }
        
        // Get recent players
        $recentPlayers = User::with(['role', 'localBody'])
            ->players()
            ->latest()
            ->take(4)
            ->get();
        
        return view('dashboard', compact('leagues', 'userLeagues', 'userOwnedTeams', 'playerInfo', 'recentPlayers'));
    }
}
