<?php

namespace App\Http\Controllers;

use App\Events\AuctionPlayerBidCall;
use App\Events\LeagueAuctionStarted;
use App\Events\LeaguePlayerAuctionStarted;
use App\Models\Auction;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
    /**
     * Display the auction bidding page.
     */
    public function index(League $league)
    {
        $leaguePlayers = LeaguePlayer::where('league_id',$league->id)
            ->with(['player.position'])
            ->get();
        return view('auction.index',compact('leaguePlayers','league'));
    }

    /**
     * Start the auction.
     */
    public function start(Request $request)
    {
        LeaguePlayerAuctionStarted::dispatch($request->all());
        $league = League::find($request->league_id);
        $league->update([
            'status'    => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Auction started successfully!',
            'auction_status' => 'active'
        ]);
    }
    /**
     * Call Bid auction.
     */
    public function call(Request $request)
    {
        $newBid =  $request->base_price + $request->increment;
        
        // Find the league team where the current user is the assigned auctioneer
        $bidTeam = LeagueTeam::where('league_id', $request->league_id)
            ->where('auctioneer_id', auth()->user()->id)
            ->first();

        // If no auctioneer is assigned, fall back to team owner (backward compatibility)
        if (!$bidTeam) {
            $bidTeam = LeagueTeam::where('league_id', $request->league_id)
                ->whereHas('team', function($query) {
                    $query->where('owner_id', auth()->user()->id);
                })->first();
        }

        if (!$bidTeam) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to bid for any team in this league.'
            ], 403);
        }

        $bidTeam->decrement('wallet_balance', $newBid);
        
        if(Auction::where('league_player_id', $request->league_player_id)->count() != 0){
            info($request->league_player_id);
            $previousBid = Auction::where('league_player_id', $request->league_player_id)->latest('id')->first();
            LeagueTeam::find($previousBid->league_team_id)->increment('wallet_balance', $previousBid->amount);
        }

        AuctionPlayerBidCall::dispatch($newBid, $bidTeam->id);

        Auction::create([
            'league_player_id'  => $request->league_player_id,
            'league_team_id'  => $bidTeam->id,
            'amount'    => $newBid
        ]);

        return response()->json([
            'success' => true,
            'call_team_id'  => $bidTeam->id,
            'message' => 'Auction bid call success',
            'auction_status' => 'active'
        ]);
    }

    public function sold(Request $request){
        info($request->all());
        LeaguePlayer::where('id',$request->league_player_id)->update([
            'league_team_id'    => $request->team_id,
            'status'    => 'sold'
        ]);
    }
}
