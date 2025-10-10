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
        // Only show leagues with approved organizer requests to prevent flooding
        $leagues = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })->with(['game', 'approvedOrganizers'])->latest()->take(5)->get();
        
        $userLeagues = auth()->user()->isOrganizer() ? 
            auth()->user()->organizedLeagues()->with(['game', 'approvedOrganizers', 'localBody.district', 'leagueTeams', 'leaguePlayers'])->get() : 
            League::whereHas('organizers', function($query) {
                $query->where('status', 'approved');
            })->with(['game', 'approvedOrganizers', 'localBody.district', 'leagueTeams', 'leaguePlayers'])->paginate(12);
        $userOwnedTeams = Auth::check() ? Auth::user()->primaryOwnedTeams : collect();
        
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
        // Get live auctions - only show when auction is actually started
        // Conditions: auction_access_granted = true, auction_active = true, and auction_started_at is not null
        $liveAuctions = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })->with(['game', 'leagueTeams.team', 'leaguePlayers.user'])
            ->where('auction_access_granted', true)
            ->where('auction_active', true)
            ->whereNotNull('auction_started_at')
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

        // Get upcoming auctions - show leagues with auction access granted but not yet started
        // Conditions: auction_access_granted = true, auction_active = false
        $upcomingAuctions = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })->with(['game', 'leagueTeams.team', 'leaguePlayers.user'])
            ->where('auction_access_granted', true)
            ->where('auction_active', false)
            ->latest()
            ->get();

        // Get leagues for filter dropdown (only approved organizers)
        $leagues = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })->get();

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
        
        // Get the current player being auctioned (first available player)
        $currentPlayer = \App\Models\LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'available')
            ->with(['player.position'])
            ->first();
            
        // Get current highest bid for the current player if exists
        $currentHighestBid = null;
        if ($currentPlayer) {
            $currentHighestBid = \App\Models\Auction::where('league_player_id', $currentPlayer->id)
                ->with(['leagueTeam.team'])
                ->latest('created_at')
                ->first();
        }
        
        // Get current highest bids for each player
        $currentBids = \App\Models\Auction::with(['leagueTeam.team', 'leaguePlayer.user'])
            ->whereHas('leaguePlayer', function($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->latest()
            ->get()
            ->groupBy('league_player_id');

        return view('auction.live', compact('league', 'currentBids', 'currentPlayer', 'currentHighestBid'));
    }
}
