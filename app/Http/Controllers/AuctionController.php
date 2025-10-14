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
        // Only show available players (exclude sold, unsold, retained, and auctioning players)
        $leaguePlayers = LeaguePlayer::where('league_id',$league->id)
            ->where('status', 'available')
            ->where('retention', false) // Exclude retained players
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
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
        
        // Get all teams with their players (retention + auctioned) for the teams section
        $teams = LeagueTeam::where('league_id', $league->id)
            ->with([
                'team',
                'leaguePlayers' => function($query) {
                    $query->with(['player.position', 'player.primaryGameRole.gamePosition'])
                          ->whereIn('status', ['retained', 'sold'])
                          ->orWhere('retention', true) // Include retained players regardless of status
                          ->orderByRaw("FIELD(status, 'retained', 'sold')")
                          ->orderBy('bid_price', 'desc');
                }
            ])
            ->withCount('leaguePlayers')
            ->get();
            
        return view('auction.index', compact('leaguePlayers', 'league', 'currentPlayer', 'currentHighestBid', 'teams'));
    }

    /**
     * Start the auction.
     */
    public function start(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'league_player_id' => 'required|exists:league_players,id',
            'player_id' => 'required|exists:users,id'
        ]);
        
        // First, reset any existing players that might be in 'auctioning' status for this league
        LeaguePlayer::where('league_id', $request->league_id)
            ->where('status', 'auctioning')
            ->update(['status' => 'available']);
        
        // Set the player status to 'auctioning' to prevent other players from being selected
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        
        if (!$leaguePlayer) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found'
            ], 404);
        }
        
        // Verify this player belongs to the specified league
        if ($leaguePlayer->league_id != $request->league_id) {
            return response()->json([
                'success' => false,
                'message' => 'Player does not belong to this league'
            ], 400);
        }
        
        // Verify player is available for auction
        if (!in_array($leaguePlayer->status, ['available', 'auctioning'])) {
            return response()->json([
                'success' => false,
                'message' => 'Player is not available for auction. Current status: ' . $leaguePlayer->status
            ], 400);
        }
        
        // Update player status to auctioning
        $leaguePlayer->update(['status' => 'auctioning']);
        
        // Update league status
        $league = League::find($request->league_id);
        if ($league) {
            $league->update([
                'status' => 'active'
            ]);
        }
        
        // Broadcast event
        LeaguePlayerAuctionStarted::dispatch($request->all());

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
        
        // Only search available players (exclude sold, unsold, retained, auctioning)
        $players = LeaguePlayer::with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->where('league_id', $leagueId)
            ->where('status', 'available') // Only show available players
            ->where('retention', false) // Exclude retained players
            ->where(function ($q) use ($query) {
                $q->whereHas('player', function ($subQ) use ($query) {
                    $subQ->where('name', 'LIKE', "%{$query}%")
                         ->orWhere('mobile', 'LIKE', "%{$query}%")
                         ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('player.position', function ($subQ) use ($query) {
                    $subQ->where('name', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('player.primaryGameRole.gamePosition', function ($subQ) use ($query) {
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
                    'position' => $leaguePlayer->player->primaryGameRole && $leaguePlayer->player->primaryGameRole->gamePosition ? $leaguePlayer->player->primaryGameRole->gamePosition->name : '',
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
        // Calculate new bid based on base price and increment
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

        // Get the league player and reload it to ensure we have consistent data
        $leaguePlayer = LeaguePlayer::with(['player', 'player.position', 'player.primaryGameRole.gamePosition'])->find($request->league_player_id);
        
        // Make sure the team is fully loaded with all necessary relationships
        $bidTeam = LeagueTeam::with(['team', 'league'])->find($bidTeam->id);
        
        // Create a bid record with consistent data
        $bidData = [
            'amount' => $newBid,
            'league_team_id' => $bidTeam->id,
            'league_team' => $bidTeam->toArray(),
            'league_player_id' => $leaguePlayer->id,
            'league_player' => $leaguePlayer->toArray(),
            'timestamp' => now()->timestamp
        ];
        
        // Store the current bid data in cache to ensure all users see the same values
        \Cache::put("auction_current_bid_{$leaguePlayer->id}", $bidData, now()->addHours(12));
        \Cache::put("auction_latest_bid", $bidData, now()->addHours(12));
        
        // Broadcast the new bid with consistent data - using broadcastNow for immediate delivery
        event(new AuctionPlayerBidCall($newBid, $bidTeam->id, $leaguePlayer->id));

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
        $overrideAmount = $request->override_amount;
        
        // Use database transaction for atomic operations
        DB::transaction(function () use ($leaguePlayerId, $teamId, $overrideAmount) {
            // Mark the winning bid as 'won'
            $winningBid = Auction::where('league_player_id', $leaguePlayerId)
                ->where('league_team_id', $teamId)
                ->latest('id')
                ->first();
                
            if ($winningBid) {
                $winningBid->update(['status' => 'won']);
            }
            
            // Mark all other bids for this player as 'lost'
            // Use the query builder with proper quoting for enum values
            $otherBids = Auction::where('league_player_id', $leaguePlayerId)
                ->where('id', '!=', $winningBid ? $winningBid->id : 0)
                ->get();
            
            foreach ($otherBids as $bid) {
                $bid->update(['status' => 'lost']);
            }
            
            // Determine the bid price - use override amount if provided, otherwise use winning bid amount
            $bidPrice = null;
            if ($overrideAmount) {
                $bidPrice = $overrideAmount;
            } elseif ($winningBid) {
                $bidPrice = $winningBid->amount;
            } else {
                $bidPrice = 0;
            }
            
            // Update the league player status to 'sold' and assign to team
            LeaguePlayer::where('id', $leaguePlayerId)->update([
                'league_team_id' => $teamId,
                'status' => 'sold',
                'bid_price' => $bidPrice
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
    
    /**
     * API endpoint to get recent bids for a league
     */
    public function getRecentBids(League $league)
    {
        // Use short-term caching to prevent excessive database queries
        $cacheKey = "league.{$league->slug}.recent-bids";
        $cacheDuration = 3; // 3 seconds
        
        $recentBids = \Cache::remember($cacheKey, $cacheDuration, function() use ($league) {
            return Auction::with(['leagueTeam.team', 'leaguePlayer.player'])
                ->whereHas('leagueTeam', function($query) use ($league) {
                    $query->where('league_id', $league->id);
                })
                ->latest()
                ->take(10)
                ->get();
        });
        
        return response()->json([
            'success' => true,
            'bids' => $recentBids
        ]);
    }
    
    /**
     * API endpoint to get team balances for a league
     */
    public function getTeamBalances(League $league)
    {
        // Use short-term caching to prevent excessive database queries
        $cacheKey = "league.{$league->slug}.team-balances";
        $cacheDuration = 3; // 3 seconds
        
        $teams = \Cache::remember($cacheKey, $cacheDuration, function() use ($league) {
            return LeagueTeam::where('league_id', $league->id)
                ->with(['team'])
                ->withCount('leaguePlayers as players_count')
                ->get()
                ->map(function($leagueTeam) {
                    return [
                        'id' => $leagueTeam->id,
                        'name' => $leagueTeam->team->name,
                        'wallet_balance' => $leagueTeam->wallet_balance,
                        'players_count' => $leagueTeam->players_count
                    ];
                });
        });
        
        return response()->json([
            'success' => true,
            'teams' => $teams
        ]);
    }
}
