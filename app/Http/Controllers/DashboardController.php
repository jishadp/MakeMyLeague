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
        $leagues = League::with(['game', 'approvedOrganizers'])->latest()->take(5)->get();
        $userLeagues = auth()->user()->isOrganizer() ? 
            auth()->user()->organizedLeagues()->with(['game', 'approvedOrganizers', 'localBody.district', 'leagueTeams', 'leaguePlayers'])->get() : 
            League::with(['game', 'approvedOrganizers', 'localBody.district', 'leagueTeams', 'leaguePlayers'])->get();
        $userOwnedTeams = Auth::check() ? Team::where('owner_id', Auth::id())->get() : collect();
        
        // Get league teams where user is a player
        $userLeagueTeams = Auth::check() ? 
            \App\Models\LeaguePlayer::where('user_id', Auth::id())
                ->with(['leagueTeam.team.localBody', 'leagueTeam.league', 'player.position'])
                ->get() : collect();

        // Get player info if the current user has a position_id (is a player)
        $playerInfo = null;
        if (Auth::check() && Auth::user()->position_id) {
            $playerInfo = Auth::user()->load(['position', 'localBody']);
        }

        return view('dashboard', compact('leagues', 'userLeagues', 'userOwnedTeams', 'userLeagueTeams', 'playerInfo'));
    }

    /**
     * Display the auction listing page.
     */
    public function auctionsIndex()
    {
        // Get live auctions (leagues with active status and auction_active = true)
        $liveAuctions = League::with(['game', 'leagueTeams.team', 'leaguePlayers.user'])
            ->where('status', 'active')
            ->where('auction_active', true)
            ->get();

        // Get past auctions (completed auction bids with relationships)
        $pastAuctions = Auction::with([
            'leaguePlayer.user',
            'leagueTeam.team',
            'leagueTeam.league'
        ])
        ->where('status', 'won')
        ->latest()
        ->paginate(10);

        // Get upcoming auctions (leagues that are pending and have auction_active = false)
        $upcomingAuctions = League::with(['game', 'leagueTeams.team', 'leaguePlayers.user'])
            ->where('status', 'pending')
            ->where('auction_active', false)
            ->latest()
            ->get();

        // Get leagues for filter dropdown
        $leagues = League::all();

        // Get teams for filter dropdown
        $teams = LeagueTeam::with('team')->get();

        return view('dashboard.auctions.index', compact('liveAuctions', 'pastAuctions', 'upcomingAuctions', 'leagues', 'teams'));
    }

    /**
     * Display the public live auction view.
     */
    public function liveAuction(League $league)
    {
        $league->load(['game', 'leagueTeams.team', 'leaguePlayers.user']);
        
        // Get current highest bids for each player
        $currentBids = Auction::with(['leagueTeam.team', 'leaguePlayer.user'])
            ->whereHas('leaguePlayer', function($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->latest()
            ->get()
            ->groupBy('league_player_id');

        return view('auction.live', compact('league', 'currentBids'));
    }
}
