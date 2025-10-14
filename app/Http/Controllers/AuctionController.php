<?php

namespace App\Http\Controllers;

use App\Events\AuctionPlayerBidCall;
use App\Events\LeagueAuctionStarted;
use App\Events\LeaguePlayerAuctionStarted;
use App\Events\PlayerSold;
use App\Events\PlayerUnsold;
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
            
        // Get the currently auctioning player first, then available player
        $currentPlayer = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->with(['player.position'])
            ->first();
            
        // Get the current highest bid for the current player if exists
        $currentHighestBid = null;
        if ($currentPlayer) {
            $currentHighestBid = Auction::where('league_player_id', $currentPlayer->id)
                ->with(['leagueTeam.team'])
                ->latest('created_at')
                ->first();
        }
            
        return view('auction.index', compact('leaguePlayers', 'league', 'currentPlayer', 'currentHighestBid'));
    }

    /**
     * Start the auction.
     */
    public function start(Request $request)
    {
        // Set the player status to 'auctioning' to prevent other players from being selected
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        if ($leaguePlayer) {
            $leaguePlayer->update(['status' => 'auctioning']);
        }
        
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
            ->whereIn('status', ['available', 'auctioning']) // Include auctioning players for search
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
        $newBid = $request->base_price + $request->increment;
        $user = auth()->user();
        
        // Validate that the player is currently being auctioned
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        if (!$leaguePlayer || $leaguePlayer->status !== 'auctioning') {
            return response()->json([
                'success' => false,
                'message' => 'This player is not currently being auctioned.'
            ], 400);
        }
        
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
                        $query->whereHas('owners', function($q) use ($user) {
                            $q->where('user_id', $user->id)->where('role', 'owner');
                        });
                    })->first();
            }
        }

        if (!$bidTeam) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to bid for any team in this league.'
            ], 403);
        }

        // Check if team has sufficient balance
        if ($bidTeam->wallet_balance < $newBid) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient team balance. Available: ₹' . $bidTeam->wallet_balance . ', Required: ₹' . $newBid
            ], 400);
        }

        // Use database transaction for atomic operations
        DB::transaction(function () use ($newBid, $bidTeam, $request) {
            // Refund previous bid if exists
            $previousBid = Auction::where('league_player_id', $request->league_player_id)
                ->latest('id')
                ->first();
                
            if ($previousBid) {
                // Refund the previous bidder
                LeagueTeam::find($previousBid->league_team_id)
                    ->increment('wallet_balance', $previousBid->amount);
            }

            // Deduct new bid from current team
            $bidTeam->decrement('wallet_balance', $newBid);

            // Create new auction record
            Auction::create([
                'league_player_id' => $request->league_player_id,
                'league_team_id' => $bidTeam->id,
                'amount' => $newBid,
                'status' => 'ask' // Current bid status
            ]);
        });

        // Broadcast the new bid
        AuctionPlayerBidCall::dispatch($newBid, $bidTeam->id);

        return response()->json([
            'success' => true,
            'call_team_id' => $bidTeam->id,
            'new_bid' => $newBid,
            'team_balance' => $bidTeam->fresh()->wallet_balance,
            'message' => 'Auction bid call success',
            'auction_status' => 'active'
        ]);
    }

    public function sold(Request $request)
    {
        $leaguePlayerId = $request->league_player_id;
        $teamId = $request->team_id;
        
        // Use database transaction for atomic operations
        DB::transaction(function () use ($leaguePlayerId, $teamId) {
            // Mark the winning bid as 'won'
            $winningBid = Auction::where('league_player_id', $leaguePlayerId)
                ->where('league_team_id', $teamId)
                ->latest('id')
                ->first();
                
            if ($winningBid) {
                $winningBid->update(['status' => 'won']);
            }
            
            // Mark all other bids for this player as 'lost'
            Auction::where('league_player_id', $leaguePlayerId)
                ->where('id', '!=', $winningBid ? $winningBid->id : 0)
                ->update(['status' => 'lost']);
            
            // Update the league player status to 'sold' and assign to team
            LeaguePlayer::where('id', $leaguePlayerId)->update([
                'league_team_id' => $teamId,
                'status' => 'sold',
                'bid_price' => $winningBid ? $winningBid->amount : 0
            ]);
        });

        // Broadcast the player sold event
        PlayerSold::dispatch($leaguePlayerId, $teamId);

        return response()->json([
            'success' => true,
            'message' => 'Player marked as sold successfully!'
        ]);
    }
    
    public function unsold(Request $request)
    {
        $leaguePlayerId = $request->league_player_id;
        
        // Use database transaction for atomic operations
        DB::transaction(function () use ($leaguePlayerId) {
            // Refund all bids for this player
            $bids = Auction::where('league_player_id', $leaguePlayerId)->get();
            
            foreach ($bids as $bid) {
                // Refund the bid amount to the team
                LeagueTeam::find($bid->league_team_id)
                    ->increment('wallet_balance', $bid->amount);
                    
                // Mark bid as refunded
                $bid->update(['status' => 'refunded']);
            }
            
            // Update the league player status to 'unsold'
            LeaguePlayer::where('id', $leaguePlayerId)->update([
                'status' => 'unsold',
                'league_team_id' => null,
                'bid_price' => null
            ]);
        });

        // Broadcast the player unsold event
        PlayerUnsold::dispatch($leaguePlayerId);

        return response()->json([
            'success' => true,
            'message' => 'Player marked as unsold successfully!'
        ]);
    }
}
