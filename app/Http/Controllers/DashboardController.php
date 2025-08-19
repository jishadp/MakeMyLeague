<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Models\GamePosition;
use App\Models\LeagueTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
            $playerInfo = Auth::user()->load(['position', 'localBody']);
        }
        
        return view('dashboard', compact('leagues', 'userLeagues', 'userOwnedTeams', 'playerInfo'));
    }
    
    /**
     * Display the auction listing page.
     */
    public function auctionsIndex()
    {
        // Get all completed auction bids with relationships
        $auctions = Auction::with([
            'leaguePlayer.user', 
            'leagueTeam.team', 
            'leagueTeam.league'
        ])
        ->where('status', 'won')
        ->latest()
        ->paginate(10);
        
        // Get leagues for filter dropdown
        $leagues = League::all();
        
        // Get teams for filter dropdown
        $teams = LeagueTeam::with('team')->get();
        
        return view('dashboard.auctions.index', compact('auctions', 'leagues', 'teams'));
    }
}
