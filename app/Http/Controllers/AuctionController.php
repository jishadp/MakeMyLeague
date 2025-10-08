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
     * Search available players for auction.
     */
    public function searchPlayers(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'league_id' => 'required|exists:leagues,id'
        ]);

        $query = $request->input('query');
        $leagueId = $request->input('league_id');
        
        $players = LeaguePlayer::with(['player.position'])
            ->where('league_id', $leagueId)
            ->where('status', 'available')
            ->where(function ($q) use ($query) {
                $q->whereHas('player', function ($subQ) use ($query) {
                    $subQ->where('name', 'LIKE', "%{$query}%")
                         ->orWhere('mobile', 'LIKE', "%{$query}%")
                         ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('player.position', function ($subQ) use ($query) {
                    $subQ->where('name', 'LIKE', "%{$query}%");
                });
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'players' => $players->map(function ($leaguePlayer) {
                return [
                    'id' => $leaguePlayer->id,
                    'user_id' => $leaguePlayer->user_id,
                    'player_name' => $leaguePlayer->player->name,
                    'mobile' => $leaguePlayer->player->mobile,
                    'email' => $leaguePlayer->player->email,
                    'position' => $leaguePlayer->player->position ? $leaguePlayer->player->position->name : 'No Position',
                    'base_price' => $leaguePlayer->base_price,
                    'photo' => $leaguePlayer->player->photo ? asset($leaguePlayer->player->photo) : asset('images/defaultplayer.jpeg')
                ];
            })
        ]);
    }
    /**
     * Call Bid auction.
     */
    public function call(Request $request)
    {
        $newBid =  $request->base_price + $request->increment;
        $user = auth()->user();
        
        // Find the league team where the current user is the assigned auctioneer
        $bidTeam = LeagueTeam::where('league_id', $request->league_id)
            ->where('auctioneer_id', $user->id)
            ->first();

        // If no auctioneer is assigned, use default team logic
        if (!$bidTeam) {
            // Check if user has multiple teams in this league
            if ($user->hasMultipleTeamsInLeague($request->league_id)) {
                // Use default team for this league
                $defaultTeam = $user->getDefaultTeamForLeague($request->league_id);
                if ($defaultTeam) {
                    $bidTeam = LeagueTeam::where('league_id', $request->league_id)
                        ->where('team_id', $defaultTeam->id)
                        ->first();
                }
            } else {
                // Fall back to team owner (backward compatibility for single team)
                $bidTeam = LeagueTeam::where('league_id', $request->league_id)
                    ->whereHas('team', function($query) use ($user) {
                        $query->where('owner_id', $user->id);
                    })->first();
            }
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
