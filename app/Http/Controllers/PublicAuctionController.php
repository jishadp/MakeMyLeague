<?php

namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;

class PublicAuctionController extends Controller
{
    public function index()
    {
        // Get all leagues with active auctions
        $activeAuctions = League::where('auction_started', true)
                               ->orderBy('updated_at', 'desc')
                               ->get();
        
        return view('auctions.index', compact('activeAuctions'));
    }
}
