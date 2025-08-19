<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuctionsController extends Controller
{
    /**
     * Display a listing of the auctions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        // Get featured players
        $featuredPlayers = User::with(['position', 'localBody'])
            ->players()
            ->take(4)
            ->get();

        return view('auctions.index', compact( 'featuredPlayers'));
    }

    /**
     * Display the specified auction.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Get featured players
        $availablePlayers = User::with(['position', 'localBody'])
            ->players()
            ->take(8)
            ->get();

        return view('auctions.show', compact('availablePlayers'));
    }   
}
