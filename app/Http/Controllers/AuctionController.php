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
use App\Services\AuctionAccessService;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }
    /**
     * Display the auction bidding page.
     */
    public function index(League $league)
    {
        $this->authorize('viewAuctionPanel', $league);
        
        $user = auth()->user();
        $accessCheck = $this->auctionAccessService->canUserAccessAuction($user->id, $league->id);
        $userRole = $accessCheck['role'];
        $userTeamId = $accessCheck['team_id'];
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
                'auctioneer', // Include auctioneer information
                'teamAuctioneer.auctioneer', // Include active team auctioneer
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
        
        // Get user's auctioneer assignment for this league
        $userAuctioneerAssignment = null;
        if (auth()->check()) {
            $userAuctioneerAssignment = \App\Models\TeamAuctioneer::where('auctioneer_id', auth()->id())
                ->where('status', 'active')
                ->whereHas('leagueTeam', function($query) use ($league) {
                    $query->where('league_id', $league->id);
                })
                ->with(['leagueTeam.team'])
                ->first();
        }
            
        return view('auction.index', compact('leaguePlayers', 'league', 'currentPlayer', 'currentHighestBid', 'teams', 'userAuctioneerAssignment', 'userRole', 'userTeamId'));
    }

    /**
     * Show the organizer/admin control room for managing live auctions.
     */
    public function controlRoom(League $league)
    {
        $user = auth()->user();

        if (!$user || !$user->canManageLeague($league->id)) {
            abort(403, 'Only league organizers or admins can access the auction control room.');
        }

        $this->authorize('viewAuctionPanel', $league);

        $league->load(['game', 'localBody.district']);
        $league->loadCount('leagueTeams');

        $currentPlayer = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->first();

        $currentHighestBid = null;

        if ($currentPlayer) {
            $currentHighestBid = Auction::where('league_player_id', $currentPlayer->id)
                ->with(['leagueTeam.team'])
                ->latest('created_at')
                ->first();
        }

        $recentBids = Auction::with(['leagueTeam.team', 'leaguePlayer.player'])
            ->whereHas('leaguePlayer', function ($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->latest('created_at')
            ->take(12)
            ->get();

        $availablePlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'available')
            ->with(['player.position'])
            ->orderBy('updated_at', 'asc')
            ->take(8)
            ->get();

        $teams = LeagueTeam::where('league_id', $league->id)
            ->with(['team', 'teamAuctioneer.auctioneer'])
            ->withCount([
                'leaguePlayers as total_players_count',
                'leaguePlayers as sold_players_count' => function ($query) {
                    $query->where('status', 'sold');
                }
            ])
            ->withSum([
                'leaguePlayers as spent_amount' => function ($query) {
                    $query->where('status', 'sold');
                }
            ], 'bid_price')
            ->orderBy('team_id')
            ->get();
        $availableBasePrices = $league->leaguePlayers()
            ->where('status', 'available')
            ->orderBy('base_price')
            ->pluck('base_price')
            ->map(fn ($price) => max((float) $price, 0))
            ->values();
        $retainedCounts = \App\Models\LeaguePlayer::where('league_id', $league->id)
            ->where('retention', true)
            ->groupBy('league_team_id')
            ->select('league_team_id', DB::raw('count(*) as count'))
            ->pluck('count', 'league_team_id');
        $auctionSlotsPerTeam = max(($league->max_team_players ?? 0) - ($league->retention_players ?? 0), 0);
        $teams = $teams->map(function ($team) use ($availableBasePrices, $auctionSlotsPerTeam, $league, $retainedCounts) {
            $retainedCount = $retainedCounts[$team->id] ?? 0;
            $playersNeeded = max($auctionSlotsPerTeam - ($team->sold_players_count ?? 0), 0);
            $futureSlots = max($playersNeeded - 1, 0);
            $reserveAmount = $futureSlots > 0 ? $availableBasePrices->take($futureSlots)->sum() : 0;
            $baseWallet = $league->team_wallet_limit ?? ($team->wallet_balance ?? 0);
            $spentAmount = (float) ($team->spent_amount ?? 0);
            $availableWallet = max($baseWallet - $spentAmount, 0);
            $maxBidCap = max($availableWallet - $reserveAmount, 0);
            $team->players_needed = $playersNeeded;
            $team->reserve_amount = $reserveAmount;
            $team->max_bid_cap = $maxBidCap;
            $team->display_wallet = $availableWallet;
            $team->retained_players_count = $retainedCount;
            $team->balance_audit = [
                'base_wallet' => $baseWallet,
                'spent_amount' => $spentAmount,
                'calculated_balance' => $availableWallet,
                'stored_balance' => $team->wallet_balance ?? 0,
                'difference' => $availableWallet - ($team->wallet_balance ?? 0),
            ];
            return $team;
        });
        $auctionStats = [
            'total_players' => $league->leaguePlayers()->count(),
            'sold_players' => $league->leaguePlayers()->where('status', 'sold')->count(),
            'available_players' => $league->leaguePlayers()->where('status', 'available')->count(),
            'unsold_players' => $league->leaguePlayers()->where('status', 'unsold')->count(),
            'wallet_spent' => $league->leaguePlayers()->where('status', 'sold')->sum('bid_price'),
            'wallet_remaining' => $teams->sum(fn ($team) => $team->wallet_balance ?? 0),
        ];

        $progressPercentage = $auctionStats['total_players'] > 0
            ? round(($auctionStats['sold_players'] / $auctionStats['total_players']) * 100)
            : 0;

        $bidIncrements = $league->bid_increment_type === 'predefined' && !empty($league->predefined_increments)
            ? $league->predefined_increments
            : [500, 1000, 2000, 5000];

        $switchableLeagues = $user->isAdmin()
            ? League::orderBy('name')->get(['id', 'name', 'slug'])
            : $user->approvedOrganizedLeagues()->orderBy('name')->get();

        return view('auction.back-controller', [
            'league' => $league,
            'currentPlayer' => $currentPlayer,
            'currentHighestBid' => $currentHighestBid,
            'availablePlayers' => $availablePlayers,
            'teams' => $teams,
            'recentBids' => $recentBids,
            'auctionStats' => $auctionStats,
            'progressPercentage' => $progressPercentage,
            'bidIncrements' => $bidIncrements,
            'switchableLeagues' => $switchableLeagues,
        ]);
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
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }
        
        $leaguePlayer->loadMissing('league');
        $league = $leaguePlayer->league;
        
        $this->authorize('placeBid', $leaguePlayer->league);

        $user = auth()->user();
        $newBid = $request->base_price + $request->increment;

        $bidTeam = null;

        if ($request->filled('league_team_id')) {
            if (!$user->canManageLeague($leaguePlayer->league_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only organizers or admins can place bids on behalf of other teams.'
                ], 403);
            }

            $selectedTeamId = (int) $request->league_team_id;
            $bidTeam = LeagueTeam::where('league_id', $leaguePlayer->league_id)
                ->where('id', $selectedTeamId)
                ->first();

            if (!$bidTeam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected team is not part of this league.'
                ], 422);
            }
        } else {
            // Use the new access control service to validate bid access
            $accessValidation = $this->auctionAccessService->validateBidAccess($user, $leaguePlayer);
            
            if (!$accessValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $accessValidation['message']
                ], 403);
            }

            $bidTeam = $accessValidation['league_team'];
        }

        // Check if bid team exists
        if (!$bidTeam) {
            return response()->json([
                'success' => false,
                'message' => 'You need to own a team in this league to place bids. Please register a team first.'
            ], 400);
        }

        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'bid_placed',
            'LeaguePlayer',
            $leaguePlayer->id,
            ['amount' => $newBid]
        );
        
        $currentHighestBidRecord = Auction::where('league_player_id', $leaguePlayer->id)
            ->latest('id')
            ->first();
        $refundableAmount = ($currentHighestBidRecord && $currentHighestBidRecord->league_team_id === $bidTeam->id)
            ? $currentHighestBidRecord->amount
            : 0;
        $projectedBalance = ($bidTeam->wallet_balance + $refundableAmount) - $newBid;
        $securedPlayers = $this->getSecuredRosterCount($bidTeam);
        $rosterValidation = $this->validateRosterBudget(
            $bidTeam,
            $league,
            $projectedBalance,
            $securedPlayers + 1
        );
        
        if (!$rosterValidation['ok']) {
            return response()->json([
                'success' => false,
                'message' => $rosterValidation['message']
            ], 422);
        }

        // Ensure team keeps enough balance for future auction slots based on reserve logic
        $auctionSlotsPerTeam = max(($league->max_team_players ?? 0) - ($league->retention_players ?? 0), 0);
        $soldCount = $bidTeam->leaguePlayers()->where('status', 'sold')->count();
        $futureSlots = max($auctionSlotsPerTeam - ($soldCount + 1), 0);
        if ($futureSlots > 0) {
            $availableBasePrices = $league->leaguePlayers()
                ->where('status', 'available')
                ->orderBy('base_price')
                ->pluck('base_price')
                ->map(fn ($price) => max((float) $price, 0))
                ->values();
            $reserveAmount = $availableBasePrices->take($futureSlots)->sum();

            if ($projectedBalance < $reserveAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'This bid would leave ₹' . number_format($projectedBalance)
                        . ', but at least ₹' . number_format($reserveAmount)
                        . ' is required to fill the remaining ' . $futureSlots . ' slots at base price.'
                ], 422);
            }
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
                
            if ($previousBid && $previousBid->status !== 'refunded') {
                // Refund the previous bidder only once
                LeagueTeam::find($previousBid->league_team_id)
                    ->increment('wallet_balance', $previousBid->amount);
                $previousBid->update(['status' => 'refunded']);
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
        
        // Create a comprehensive bid record with consistent data
        $bidData = [
            'amount' => $newBid,
            'league_team_id' => $bidTeam->id,
            'league_team' => [
                'id' => $bidTeam->id,
                'team' => $bidTeam->team,
                'wallet_balance' => $bidTeam->fresh()->wallet_balance,
                'league_players_count' => $bidTeam->league_players_count,
                'league_id' => $bidTeam->league_id
            ],
            'league_player_id' => $leaguePlayer->id,
            'league_player' => [
                'id' => $leaguePlayer->id,
                'player' => $leaguePlayer->player,
                'base_price' => $leaguePlayer->base_price,
                'league_id' => $leaguePlayer->league_id
            ],
            'timestamp' => now()->timestamp,
            'league_id' => $leaguePlayer->league_id
        ];
        
        // Store the current bid data in cache to ensure all users see the same values
        \Cache::put("auction_current_bid_{$leaguePlayer->id}", $bidData, now()->addHours(12));
        \Cache::put("auction_latest_bid_{$leaguePlayer->league_id}", $bidData, now()->addHours(12));
        
        // Broadcast the new bid with comprehensive data - using broadcastNow for immediate delivery
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
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }
        
        $leaguePlayer->loadMissing('league');
        
        $this->authorize('markSoldUnsold', $leaguePlayer->league);
        
        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_sold',
            'LeaguePlayer',
            $leaguePlayer->id,
            ['team_id' => $request->team_id, 'amount' => $request->override_amount]
        );
        $leaguePlayerId = $request->league_player_id;
        $teamId = $request->team_id;
        $overrideAmount = $request->override_amount;
        
        $team = LeagueTeam::find($teamId);
        if (!$team || $team->league_id !== $leaguePlayer->league_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid team selection for this league.'
            ], 422);
        }
        
        $winningBidPreview = Auction::where('league_player_id', $leaguePlayerId)
            ->where('league_team_id', $teamId)
            ->latest('id')
            ->first();
        
        if ($overrideAmount !== null && $overrideAmount !== '') {
            $bidPrice = floatval($overrideAmount);
        } elseif ($winningBidPreview) {
            $bidPrice = $winningBidPreview->amount;
        } else {
            $bidPrice = 0;
        }
        
        $currentRosterCount = $this->getSecuredRosterCount($team);
        $projectedRosterCount = $currentRosterCount + 1;
        if ($winningBidPreview) {
            $balanceAdjustmentPreview = $winningBidPreview->amount - $bidPrice;
            $projectedBalance = $team->wallet_balance + $balanceAdjustmentPreview;
        } else {
            $balanceAdjustmentPreview = -$bidPrice;
            $projectedBalance = $team->wallet_balance - $bidPrice;
        }
        
        $rosterValidation = $this->validateRosterBudget(
            $team,
            $leaguePlayer->league,
            $projectedBalance,
            $projectedRosterCount
        );
        
        if (!$rosterValidation['ok']) {
            return response()->json([
                'success' => false,
                'message' => $rosterValidation['message']
            ], 422);
        }

        // Use database transaction for atomic operations
        DB::transaction(function () use ($leaguePlayerId, $teamId, $overrideAmount, $bidPrice) {
            // Mark the winning bid as 'won'
            $winningBid = Auction::where('league_player_id', $leaguePlayerId)
                ->where('league_team_id', $teamId)
                ->latest('id')
                ->first();
                
            if ($winningBid) {
                $winningBid->update(['status' => 'won']);
            }
            
            // Mark all other bids for this player as 'lost'
            // Refund losing teams
            $otherBids = Auction::where('league_player_id', $leaguePlayerId)
                ->where('id', '!=', $winningBid ? $winningBid->id : 0)
                ->get();
            
            foreach ($otherBids as $bid) {
                // Refund the bid amount to losing teams only if not already refunded
                if ($bid->status !== 'refunded') {
                    LeagueTeam::find($bid->league_team_id)
                        ->increment('wallet_balance', $bid->amount);
                    $bid->update(['status' => 'refunded']);
                } else {
                    $bid->update(['status' => 'lost']);
                }
            }
            // Adjust winning team's balance if override amount is different from bid
            if ($winningBid) {
                if ($overrideAmount !== null && $overrideAmount !== '') {
                    $balanceAdjustment = $winningBid->amount - $bidPrice;
                    if ($balanceAdjustment != 0) {
                        // If override is less than bid, refund difference
                        // If override is more than bid, deduct extra
                        LeagueTeam::find($teamId)->increment('wallet_balance', $balanceAdjustment);
                    }
                }
            } elseif ($bidPrice > 0) {
                // No bid existed – deduct the manual sale amount directly
                LeagueTeam::find($teamId)->decrement('wallet_balance', $bidPrice);
            }
            
            // Update the league player status to 'sold' and assign to team
            LeaguePlayer::where('id', $leaguePlayerId)->update([
                'league_team_id' => $teamId,
                'status' => 'sold',
                'bid_price' => $bidPrice
            ]);
        });

        // Get updated team balance for response
        $updatedTeam = LeagueTeam::with('team')->find($teamId);

        // Broadcast the player sold event
        PlayerSold::dispatch($leaguePlayerId, $teamId);

        return response()->json([
            'success' => true,
            'message' => 'Player marked as sold successfully!',
            'team_balance' => $updatedTeam->wallet_balance,
            'team_id' => $teamId,
            'team_name' => $updatedTeam->team->name ?? 'Unknown'
        ]);
    }
    
    public function unsold(Request $request)
    {
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }
        
        $this->authorize('markSoldUnsold', $leaguePlayer->league);
        
        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_unsold',
            'LeaguePlayer',
            $leaguePlayer->id
        );
        $leaguePlayerId = $request->league_player_id;
        
        // Use database transaction for atomic operations
        DB::transaction(function () use ($leaguePlayerId) {
            // Refund all bids for this player
            $bids = Auction::where('league_player_id', $leaguePlayerId)->get();
            
            foreach ($bids as $bid) {
                // Refund the bid amount to the team only if it has not been refunded yet
                if ($bid->status !== 'refunded') {
                    LeagueTeam::find($bid->league_team_id)
                        ->increment('wallet_balance', $bid->amount);
                }
                    
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
            $availableBasePrices = LeaguePlayer::where('league_id', $league->id)
                ->where('status', 'available')
                ->orderBy('base_price')
                ->pluck('base_price')
                ->map(fn ($price) => max((float) $price, 0))
                ->values();

            $retainedCounts = LeaguePlayer::where('league_id', $league->id)
                ->where('retention', true)
                ->groupBy('league_team_id')
                ->select('league_team_id', DB::raw('count(*) as count'))
                ->pluck('count', 'league_team_id');

            $auctionSlotsPerTeam = max(($league->max_team_players ?? 0) - ($league->retention_players ?? 0), 0);

            return LeagueTeam::where('league_id', $league->id)
                ->with(['team'])
                ->withCount([
                    'leaguePlayers as sold_players_count' => function ($query) {
                        $query->where('status', 'sold');
                    },
                    'leaguePlayers as players_count' => function ($query) {
                        $query->where(function ($q) {
                            $q->whereIn('status', ['retained', 'sold'])
                              ->orWhere('retention', true);
                        });
                    },
                ])
                ->withSum([
                    'leaguePlayers as spent_amount' => function ($query) {
                        $query->where(function ($q) {
                            $q->whereIn('status', ['retained', 'sold'])
                              ->orWhere('retention', true);
                        });
                    },
                ], 'bid_price')
                ->get()
                ->map(function ($leagueTeam) use ($availableBasePrices, $league, $retainedCounts, $auctionSlotsPerTeam) {
                    $soldCount = (int) ($leagueTeam->sold_players_count ?? 0);
                    $retainedCount = (int) ($retainedCounts[$leagueTeam->id] ?? 0);
                    $playersNeeded = max($auctionSlotsPerTeam - $soldCount, 0);
                    $futureSlots = max($playersNeeded - 1, 0);
                    $reserveAmount = $futureSlots > 0 ? $availableBasePrices->take($futureSlots)->sum() : 0;
                    $spentAmount = (float) ($leagueTeam->spent_amount ?? 0);
                    $baseWallet = $league->team_wallet_limit ?? ($leagueTeam->wallet_balance ?? 0);
                    $availableWallet = max($baseWallet - $spentAmount, 0);
                    $maxBidCap = max($availableWallet - $reserveAmount, 0);

                    return [
                        'id' => $leagueTeam->id,
                        'name' => $leagueTeam->team->name,
                        'wallet_balance' => $availableWallet,
                        'players_count' => (int) ($leagueTeam->players_count ?? 0),
                        'players_needed' => $playersNeeded,
                        'reserve_amount' => $reserveAmount,
                        'max_bid_cap' => $maxBidCap,
                        'retained_players_count' => $retainedCount,
                        'sold_players_count' => $soldCount,
                    ];
                });
        });
        
        return response()->json([
            'success' => true,
            'teams' => $teams
        ]);
    }

    /**
     * API endpoint to get auction access information for the current user
     */
    public function getAuctionAccess(League $league)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        // Check if user can bid in this league
        $canBid = $this->auctionAccessService->canUserBidInLeague($user, $league);
        
        // Get user's teams in this league
        $userTeams = $this->auctionAccessService->getUserTeamsInLeague($user, $league);
        
        // Get the team user should bid for
        $biddingTeam = $this->auctionAccessService->getUserBiddingTeam($user, $league);
        
        // Get auction access statistics
        $stats = $this->auctionAccessService->getAuctionAccessStats($league);

        return response()->json([
            'success' => true,
            'can_bid' => $canBid,
            'user_teams' => $userTeams->map(function($team) {
                return [
                    'league_team_id' => $team['league_team']->id,
                    'team_id' => $team['team']->id,
                    'team_name' => $team['team']->name,
                    'access_type' => $team['access_type'],
                    'access_source' => $team['access_source']
                ];
            }),
            'bidding_team' => $biddingTeam ? [
                'league_team_id' => $biddingTeam->id,
                'team_id' => $biddingTeam->team->id,
                'team_name' => $biddingTeam->team->name,
                'wallet_balance' => $biddingTeam->wallet_balance
            ] : null,
            'auction_stats' => $stats
        ]);
    }

    /**
     * Determine the minimum roster size a team must plan for.
     */
    protected function getMinimumRosterSize(League $league): int
    {
        $perTeamTarget = (int) ($league->max_team_players ?? 0);

        return max(0, $perTeamTarget);
    }

    /**
     * Count players already secured (sold or retained) by the team.
     */
    protected function getSecuredRosterCount(LeagueTeam $team): int
    {
        return $team->leaguePlayers()
            ->where(function ($query) {
                $query->whereIn('status', ['sold', 'retained'])
                    ->orWhere('retention', true);
            })
            ->count();
    }

    /**
     * Ensure a team keeps enough balance to finish its roster with available players.
     */
    protected function validateRosterBudget(LeagueTeam $team, League $league, float $projectedBalance, int $projectedRosterCount): array
    {
        $minimumRosterSize = $this->getMinimumRosterSize($league);
        $remainingSlots = max($minimumRosterSize - $projectedRosterCount, 0);

        if ($projectedBalance < 0) {
            return [
                'ok' => false,
                'message' => 'Insufficient wallet balance for this action.'
            ];
        }

        if ($remainingSlots === 0) {
            return ['ok' => true];
        }

        $availablePrices = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'available')
            ->orderBy('base_price')
            ->limit($remainingSlots)
            ->pluck('base_price');

        if ($availablePrices->count() < $remainingSlots) {
            return [
                'ok' => false,
                'message' => 'Only ' . $availablePrices->count() . ' available players remain, but '
                    . $remainingSlots . ' roster slots are still required to hit the minimum of '
                    . $minimumRosterSize . '.'
            ];
        }

        $minimumBudgetNeeded = $availablePrices->map(fn ($price) => (float) ($price ?? 0))->sum();

        if ($projectedBalance < $minimumBudgetNeeded) {
            return [
                'ok' => false,
                'message' => 'This action would leave ₹' . number_format($projectedBalance)
                    . ', but at least ₹' . number_format($minimumBudgetNeeded)
                    . ' is needed to sign the remaining ' . $remainingSlots
                    . ' players at their base prices.'
            ];
        }

        return ['ok' => true];
    }
}
