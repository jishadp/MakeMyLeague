<?php

namespace App\Http\Controllers;

use App\Events\AuctionPlayerBidCall;
use App\Events\LeagueAuctionStarted;
use App\Events\LeaguePlayerAuctionStarted;
use App\Events\PlayerSold;
use App\Events\PlayerUnsold;
use App\Models\Auction;
use App\Models\Fixture;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Services\AuctionAccessService;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    /**
     * Live matches hub for organizers/admins.
     */
    public function liveMatchesIndex(Request $request)
    {
        $leagues = League::with('game')
            ->whereHas('fixtures', function ($query) {
                $query->whereIn('status', ['in_progress', 'scheduled', 'unscheduled']);
            })
            ->orderBy('name')
            ->get();

        $fixtures = Fixture::with(['homeTeam.team', 'awayTeam.team', 'league'])
            ->whereIn('league_id', $leagues->pluck('id'))
            ->whereIn('status', ['in_progress', 'scheduled', 'unscheduled'])
            ->orderByRaw("FIELD(status, 'in_progress', 'scheduled', 'unscheduled')")
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get()
            ->groupBy('league_id');

        return view('auction.live-matches', compact('leagues', 'fixtures'));
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

        $league->load(['game', 'localBody.district', 'approvedOrganizers']);
        $league->loadCount('leagueTeams');

        $currentPlayer = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->where('retention', false)
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
            ->where('retention', false)
            ->with(['player.position'])
            ->orderBy('updated_at', 'asc')
            ->take(8)
            ->get();
        $unsoldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'unsold')
            ->where('retention', false)
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

        // Matches: surface live and upcoming fixtures for this league
        $baseFixtureQuery = Fixture::with([
            'homeTeam.team',
            'awayTeam.team',
            'leagueGroup',
        ])->where('league_id', $league->id);

        $liveFixtures = (clone $baseFixtureQuery)
            ->where('status', 'in_progress')
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->take(2)
            ->get();

        $upcomingFixtures = (clone $baseFixtureQuery)
            ->whereIn('status', ['scheduled', 'unscheduled'])
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->take(5)
            ->get();

        return view('auction.back-controller', [
            'league' => $league,
            'currentPlayer' => $currentPlayer,
            'currentHighestBid' => $currentHighestBid,
            'availablePlayers' => $availablePlayers,
            'unsoldPlayers' => $unsoldPlayers,
            'teams' => $teams,
            'recentBids' => $recentBids,
            'auctionStats' => $auctionStats,
            'progressPercentage' => $progressPercentage,
            'bidIncrements' => $bidIncrements,
            'switchableLeagues' => $switchableLeagues,
            'liveFixtures' => $liveFixtures,
            'upcomingFixtures' => $upcomingFixtures,
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
        
        // Verify player is available for auction (supports unsold round)
        if (!in_array($leaguePlayer->status, ['available', 'auctioning', 'unsold'])) {
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
        
        $hasAvailable = LeaguePlayer::where('league_id', $leagueId)
            ->where('status', 'available')
            ->exists();
        $searchStatuses = $hasAvailable ? ['available'] : ['unsold'];

        // Search available players; if none remain, fall back to unsold players
        $players = LeaguePlayer::with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->where('league_id', $leagueId)
            ->whereIn('status', $searchStatuses)
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
        
        $user = auth()->user();
        
        if (!$user) {
             \Log::warning("Bid Attempt Failed: Unauthenticated user.");
             return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        \Log::info("Bid Attempt: User {$user->id} on Player {$leaguePlayer->id} in League {$league->id}");

        try {
            $this->authorize('placeBid', $leaguePlayer->league);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Log::error("Bid Authorization Failed: User {$user->id} cannot placeBid in League {$league->id}. Error: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Authorization failed: You do not have permission to place bids in this league.'], 403);
        }

        $newBid = $request->base_price + $request->increment;

        $bidTeam = null;

        if ($request->filled('league_team_id')) {
            if (!$user->canManageLeague($leaguePlayer->league_id)) {
                \Log::warning("Bid Failed: User {$user->id} tried to manage league team without permission for league {$leaguePlayer->league_id}");
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
                \Log::warning("Bid Validation Failed for User {$user->id} on Player {$leaguePlayer->id}: " . $accessValidation['message']);
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
            $securedPlayers + 1,
            [$leaguePlayer->id]
        );
        $displayMaxCap = $this->calculateDisplayedMaxBidCap($bidTeam, $league);
        
        if (!$rosterValidation['ok'] && $newBid <= $displayMaxCap) {
            $rosterValidation['ok'] = true;
        }

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
        $validated = $request->validate([
            'league_player_id' => ['required', 'integer', 'exists:league_players,id'],
            'team_id' => ['required', 'integer', 'exists:league_teams,id'],
            'override_amount' => ['nullable', 'numeric', 'min:0'],
            'final_amount' => ['nullable', 'numeric', 'min:0'],
            'player_base_price' => ['nullable', 'numeric', 'min:0'],
            'current_bid_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $errorMessage = 'Unknown error';
    try {
        $leaguePlayer = LeaguePlayer::find($validated['league_player_id']);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if ($leaguePlayer->status === 'sold') {
            return response()->json(['success' => false, 'message' => 'Player already sold.'], 422);
        }

        $leaguePlayer->loadMissing('league');
        
        if (!$leaguePlayer->league) {
             return response()->json(['success' => false, 'message' => 'Player league relationship missing.'], 500);
        }

        $this->authorize('markSoldUnsold', $leaguePlayer->league);

        $leaguePlayerId = $validated['league_player_id'];
        $teamId = $validated['team_id'];
        $overrideInput = $validated['override_amount'] ?? null;
        $hasOverride = $overrideInput !== null && $overrideInput !== '';
        $overrideAmount = $hasOverride ? (float) $overrideInput : null;
        $basePrice = (float) ($validated['player_base_price'] ?? $leaguePlayer->base_price ?? 0);

        $team = LeagueTeam::find($teamId);
        if (!$team || $team->league_id !== $leaguePlayer->league_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid team selection for this league.'
            ], 422);
        }

        $playersNeeded = $this->getTeamPlayersNeeded($team, $leaguePlayer->league);
        if ($playersNeeded <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Team already completed their required players.'
            ], 422);
        }

        $winningBidPreview = Auction::where('league_player_id', $leaguePlayerId)
            ->where('league_team_id', $teamId)
            ->latest('id')
            ->first();
        $alreadyDeducted = (float) ($winningBidPreview->amount ?? 0);

        $finalAmount = $validated['final_amount']
            ?? ($hasOverride ? $overrideAmount : null)
            ?? ($alreadyDeducted ?: null)
            ?? ($validated['current_bid_amount'] ?? null)
            ?? $basePrice;
        $finalAmount = max((float) $finalAmount, 0);

        $minimumRosterSize = $this->getMinimumRosterSize($leaguePlayer->league);
        $currentRosterCount = $this->getSecuredRosterCount($team);
        $projectedRosterCount = $currentRosterCount + 1;
        $isFinalSlot = $minimumRosterSize > 0
            && $currentRosterCount < $minimumRosterSize
            && $projectedRosterCount >= $minimumRosterSize;
        $totalRemainingBalance = max((float) ($team->wallet_balance ?? 0), 0) + $alreadyDeducted;

        if ($isFinalSlot || $playersNeeded === 1) {
            if ($totalRemainingBalance > $finalAmount) {
                $finalAmount = $totalRemainingBalance;
                $hasOverride = true;
            }
        }

        if ($finalAmount < $basePrice) {
            return response()->json([
                'success' => false,
                'message' => 'Bid amount is below base price.'
            ], 422);
        }

        $remainingNeededAfterSale = max($playersNeeded - 1, 0);

        $availableWallet = $this->calculateAvailableWallet($team, $leaguePlayer->league);
        $availableWallet = max($availableWallet, $totalRemainingBalance);
        $maxBidCap = $this->calculateMaxBidCap($team, $leaguePlayer->league, $playersNeeded, $availableWallet);
        $displayMaxCap = $this->calculateDisplayedMaxBidCap($team, $leaguePlayer->league, $availableWallet);
        if ($remainingNeededAfterSale > 0) {
            if ($maxBidCap > 0 && $finalAmount > $maxBidCap && $finalAmount > $displayMaxCap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bid exceeds team cap/balance.'
                ], 422);
            }
        }

        $extraNeeded = max($finalAmount - $alreadyDeducted, 0);
        if ($extraNeeded > $availableWallet) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient team balance for this sale.'
            ], 422);
        }

        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_sold',
            'LeaguePlayer',
            $leaguePlayer->id,
            ['team_id' => $teamId, 'amount' => $finalAmount]
        );

        $balanceAdjustment = $alreadyDeducted - $finalAmount; // positive = refund, negative = extra deduction
        $projectedBalance = $availableWallet + $balanceAdjustment;
        $rosterValidation = $remainingNeededAfterSale <= 0
            ? ['ok' => true]
            : $this->validateRosterBudget(
                $team,
                $leaguePlayer->league,
                $projectedBalance,
                $projectedRosterCount,
                [$leaguePlayerId]
            );

        if (!$rosterValidation['ok']) {
            return response()->json([
                'success' => false,
                'message' => $rosterValidation['message']
            ], 422);
        }

        DB::transaction(function () use ($leaguePlayer, $team, $finalAmount, $teamId, $leaguePlayerId) {
            // Refund all other bids for this player
            $bids = Auction::where('league_player_id', $leaguePlayer->id)->get();
            
            // Winning Bid (if any)
            $winningBid = $bids->first(function($bid) use ($team) {
                return $bid->league_team_id == $team->id;
            });

            foreach ($bids as $bid) {
                if ($bid->status === 'refunded') continue;

                if ($bid->league_team_id != $team->id) {
                     LeagueTeam::where('id', $bid->league_team_id)->increment('wallet_balance', $bid->amount);
                     $bid->update(['status' => 'refunded']);
                 } else {
                     $bid->update(['status' => 'won']); 
                 }
            }
            
            // Adjust winning team's balance if necessary (difference between already deducted and final amount)
            // If winningBid existed, its amount was deducted.
            // If no previous bid, 0 deducted.
            $alreadyDeducted = $winningBid ? $winningBid->amount : 0;
            $balanceAdjustment = $alreadyDeducted - $finalAmount;
            
            if ($balanceAdjustment != 0) {
                 $team->increment('wallet_balance', $balanceAdjustment);
            }

            // Update Player
            $leaguePlayer->update([
                'status' => 'sold',
                'league_team_id' => $team->id,
                'bid_price' => $finalAmount
            ]);
            
            // Clear caches
             \Cache::forget("auction_current_bid_{$leaguePlayer->id}");
             \Cache::forget("auction_latest_bid_{$leaguePlayer->league_id}");
        });
        
        // Broadcast
        PlayerSold::dispatch($leaguePlayerId, $teamId, $finalAmount);

        $updatedTeam = LeagueTeam::find($teamId);

        $nextPlayer = null;
        if ($request->boolean('auto_start')) {
            $nextPlayer = $this->triggerNextRandomPlayer($leaguePlayer->league);
        }

        return response()->json([
            'success' => true,
            'message' => 'Player marked as sold successfully!',
            'sold_amount' => $finalAmount,
            'team_balance' => $updatedTeam->wallet_balance,
            'team_id' => $teamId,
            'team_name' => $updatedTeam->team->name ?? 'Unknown',
            'next_player' => $nextPlayer
        ]);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Sold Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
    }
    
    public function unsold(Request $request)
    {
        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if ($leaguePlayer->status === 'sold') {
            return response()->json([
                'success' => false,
                'message' => 'Player already sold and cannot be marked unsold.'
            ], 422);
        }

        $leaguePlayer->loadMissing('league');
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

        $nextPlayer = null;
        if ($request->boolean('auto_start')) {
            $nextPlayer = $this->triggerNextRandomPlayer($leaguePlayer->league);
        }

        return response()->json([
            'success' => true,
            'message' => 'Player marked as unsold successfully!',
            'next_player' => $nextPlayer
        ]);
    }

    /**
     * Move the current auctioning player back to the available pool.
     */
    public function skipPlayer(Request $request, League $league)
    {
        $validated = $request->validate([
            'league_player_id' => ['required', 'integer', 'exists:league_players,id'],
        ]);

        $leaguePlayer = LeaguePlayer::with('league')->find($validated['league_player_id']);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if ($leaguePlayer->league_id !== $league->id) {
            return response()->json([
                'success' => false,
                'message' => 'Player does not belong to this league.'
            ], 422);
        }

        if ($leaguePlayer->status !== 'auctioning') {
            return response()->json([
                'success' => false,
                'message' => 'Only the current auctioning player can be moved back to available.'
            ], 422);
        }

        $this->authorize('markSoldUnsold', $leaguePlayer->league);

        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_returned_to_available',
            'LeaguePlayer',
            $leaguePlayer->id
        );

        DB::transaction(function () use ($leaguePlayer) {
            $bids = Auction::where('league_player_id', $leaguePlayer->id)->get();

            $refunds = [];
            foreach ($bids as $bid) {
                if ($bid->status === 'refunded') {
                    continue;
                }
                $teamId = $bid->league_team_id;
                $refunds[$teamId] = ($refunds[$teamId] ?? 0) + (float) $bid->amount;
            }

            foreach ($refunds as $teamId => $amount) {
                if ($teamId && $amount > 0) {
                    LeagueTeam::where('id', $teamId)->increment('wallet_balance', $amount);
                }
            }

            Auction::where('league_player_id', $leaguePlayer->id)->delete();

            $leaguePlayer->update([
                'status' => 'available',
                'league_team_id' => null,
                'bid_price' => null
            ]);
        });

        \Cache::forget("auction_current_bid_{$leaguePlayer->id}");
        \Cache::forget("auction_latest_bid_{$league->id}");

        $nextPlayer = null;
        if ($request->boolean('auto_start')) {
            $nextPlayer = $this->triggerNextRandomPlayer($league);
        }

        return response()->json([
            'success' => true,
            'message' => 'Player returned to available pool.',
            'next_player' => $nextPlayer
        ]);
    }

    /**
     * Reset all bids for the current auctioning player back to base.
     */
    public function resetBids(Request $request)
    {
        $validated = $request->validate([
            'league_player_id' => ['required', 'integer', 'exists:league_players,id'],
        ]);

        $leaguePlayer = LeaguePlayer::with('league')->find($validated['league_player_id']);
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if ($leaguePlayer->status !== 'auctioning') {
            return response()->json([
                'success' => false,
                'message' => 'No active auction to reset for this player.'
            ], 422);
        }

        $this->authorize('markSoldUnsold', $leaguePlayer->league);

        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'bids_reset',
            'LeaguePlayer',
            $leaguePlayer->id
        );

        DB::transaction(function () use ($leaguePlayer) {
            $bids = Auction::where('league_player_id', $leaguePlayer->id)->get();

            $refunds = [];
            foreach ($bids as $bid) {
                if ($bid->status === 'refunded') {
                    continue;
                }
                $teamId = $bid->league_team_id;
                $refunds[$teamId] = ($refunds[$teamId] ?? 0) + (float) $bid->amount;
            }

            foreach ($refunds as $teamId => $amount) {
                if ($teamId && $amount > 0) {
                    LeagueTeam::where('id', $teamId)->increment('wallet_balance', $amount);
                }
            }

            Auction::where('league_player_id', $leaguePlayer->id)->delete();

            $leaguePlayer->update([
                'status' => 'auctioning',
                'league_team_id' => null,
                'bid_price' => null
            ]);
        });

        \Cache::forget("auction_current_bid_{$leaguePlayer->id}");
        \Cache::forget("auction_latest_bid_{$leaguePlayer->league_id}");

        return response()->json([
            'success' => true,
            'message' => 'Bids reset to base price for the current player.'
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
     * Remaining players needed before this sale is applied.
     */
    protected function getTeamPlayersNeeded(LeagueTeam $team, League $league): int
    {
        $minimumRosterSize = $this->getMinimumRosterSize($league);
        $secured = $this->getSecuredRosterCount($team);

        return max($minimumRosterSize - $secured, 0);
    }

    /**
     * Calculate max bid cap after reserving cheapest slots for future picks.
     */
    protected function calculateAvailableWallet(LeagueTeam $team, League $league): float
    {
        $baseWallet = $league->team_wallet_limit ?? ($team->wallet_balance ?? 0);
        $spentAmount = $team->leaguePlayers()
            ->where('status', 'sold')
            ->sum('bid_price');

        return max($baseWallet - $spentAmount, $team->wallet_balance ?? 0, 0);
    }

    protected function calculateMaxBidCap(LeagueTeam $team, League $league, int $playersNeeded, ?float $overrideWallet = null): float
    {
        $futureSlots = max($playersNeeded - 1, 0);
        $availableBasePrices = $league->leaguePlayers()
            ->where('status', 'available')
            ->orderBy('base_price')
            ->pluck('base_price')
            ->map(fn ($price) => max((float) $price, 0))
            ->values();

        $reserveAmount = $futureSlots > 0 ? $availableBasePrices->take($futureSlots)->sum() : 0;
        $availableWallet = $overrideWallet !== null
            ? max((float) $overrideWallet, 0)
            : max((float) ($team->wallet_balance ?? 0), 0);

        return max($availableWallet - $reserveAmount, 0);
    }

    /**
     * Mirror UI max bid cap calculation to keep validations aligned with the control room.
     */
    protected function calculateDisplayedMaxBidCap(LeagueTeam $team, League $league, ?float $overrideWallet = null): float
    {
        $auctionSlotsPerTeam = max(($league->max_team_players ?? 0) - ($league->retention_players ?? 0), 0);
        $soldCount = $team->leaguePlayers()->where('status', 'sold')->count();
        $playersNeeded = max($auctionSlotsPerTeam - $soldCount, 0);
        return $this->calculateMaxBidCap($team, $league, $playersNeeded, $overrideWallet);
    }

    /**
     * Ensure a team keeps enough balance to finish its roster with available players.
     */
    protected function validateRosterBudget(LeagueTeam $team, League $league, float $projectedBalance, int $projectedRosterCount, array $excludeLeaguePlayerIds = []): array
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

        $availableQuery = LeaguePlayer::where('league_id', $league->id)
            ->whereIn('status', ['available', 'unsold'])
            ->orderBy('base_price');

        if (!empty($excludeLeaguePlayerIds)) {
            $availableQuery->whereNotIn('id', $excludeLeaguePlayerIds);
        }

        $availablePrices = $availableQuery
            ->limit($remainingSlots)
            ->pluck('base_price');

        if ($availablePrices->count() < $remainingSlots && !empty($excludeLeaguePlayerIds)) {
            // Fall back to include excluded ids (e.g., current auction player) to prevent a false block
            $availablePrices = LeaguePlayer::where('league_id', $league->id)
                ->whereIn('status', ['available', 'unsold'])
                ->orderBy('base_price')
                ->limit($remainingSlots)
                ->pluck('base_price');
        }

        if ($availablePrices->count() < $remainingSlots) {
            return [
                'ok' => false,
                'message' => 'Only ' . $availablePrices->count() . ' remaining players are available, but '
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
    /**
     * API: Get team balances and stats with detailed roster.
     */
    public function getTeamBalances(League $league)
    {
        $teams = LeagueTeam::where('league_id', $league->id)
            ->with(['team', 'leaguePlayers' => function($q) {
                $q->whereIn('status', ['sold', 'retained'])
                  ->orWhere('retention', true)
                  ->with(['player.position', 'player.primaryGameRole.gamePosition']);
            }])
            ->get()
            ->map(function ($lt) use ($league) {
                $roster = $lt->leaguePlayers->map(function($lp) {
                    // Check if player is actually retained (either status='retained' or retention flag is likely true)
                    // We assume if status is NOT unsold and retention boolean is true, it is a retained player.
                    $isRetained = ($lp->status === 'retained') || ($lp->retention == true);
                    
                    return [
                        'id' => $lp->id,
                        'name' => $lp->player->name,
                        'position' => $lp->player->primaryGameRole->gamePosition->name ?? 'Player',
                        'price' => $lp->bid_price ?? $lp->base_price ?? 0,
                        'status' => $isRetained ? 'retained' : $lp->status, // Normalize status for frontend
                        'photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                    ];
                });

                // Stats Calculation
                // Now simple filtering works because we normalized the status above
                $soldCount = $roster->where('status', 'sold')->count();
                $retainedCount = $roster->where('status', 'retained')->count();
                $totalSecured = $soldCount + $retainedCount;
                
                // Calculate Total Spent (Stats logic: Only SOLD players count towards auction spend)
                $totalSpent = $roster->where('status', 'sold')->sum('price');
                
                $maxPlayers = (int) ($league->max_team_players ?? 0);
                if ($maxPlayers == 0) $maxPlayers = 15; // Fallback default if not set
                
                $playersNeeded = max($maxPlayers - $totalSecured, 0);
                
                // Reserve calculation
                $futureSlots = max($playersNeeded - 1, 0);
                
                // Note: Performing query inside loop is not ideal for performance but functional for now.
                // Optimally should pre-fetch available base prices.
                $reserveAmount = 0;
                if ($futureSlots > 0) {
                     $reserveAmount = \App\Models\LeaguePlayer::where('league_id', $league->id)
                        ->where('status', 'available')
                        ->orderBy('base_price')
                        ->take($futureSlots)
                        ->pluck('base_price')
                        ->sum();
                }
                
                // Max Bid Cap
                // Logic: (Wallet - Reserve for future players)
                $walletBalance = (float) $lt->wallet_balance;
                $maxBidCap = max($walletBalance - $reserveAmount, 0);

                return [
                    'id' => $lt->id,
                    'name' => $lt->team->name,
                    'logo' => $lt->team->logo ? url(Storage::url($lt->team->logo)) : null,
                    'wallet_balance' => $walletBalance,
                    'players_count' => $totalSecured, // Total squad size
                    'sold_players_count' => $soldCount,
                    'retained_players_count' => $retainedCount,
                    'players_needed' => $playersNeeded,
                    'reserve_amount' => $reserveAmount,
                    'max_bid_cap' => $maxBidCap,
                    'total_spent' => $totalSpent,
                    'roster' => $roster->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'teams' => $teams
        ]);
    }

    /**
     * API: Get list of active/live auctions.
     */
    public function getLiveAuctions()
    {
        // For now, return leagues that are 'active' or have a status indicating auction in progress.
        $leagues = League::whereIn('status', ['active', 'auction'])
            ->withCount('leaguePlayers')
            ->with(['leaguePlayers' => function ($query) {
                $query->where('status', 'auctioning')
                      ->with(['player', 'auctionBids' => function ($q) {
                          $q->orderBy('amount', 'desc')->limit(1)->with('team');
                      }]);
            }])
            ->get()
            ->map(function ($league) {
                $currentPlayer = $league->leaguePlayers->first();
                $currentData = null;

                if ($currentPlayer) {
                    $highestBid = $currentPlayer->auctionBids->first();
                    $currentData = [
                        'name' => $currentPlayer->player->name ?? 'Unknown',
                        'bid' => $highestBid ? $highestBid->amount : $currentPlayer->base_price,
                        'team' => $highestBid ? ($highestBid->team->name ?? 'No Team') : 'Base Price',
                        'photo' => $currentPlayer->player->profile_photo_path ? asset('storage/' . $currentPlayer->player->profile_photo_path) : null,
                    ];
                }

                return [
                    'id' => $league->id,
                    'name' => $league->name,
                    'slug' => $league->slug,
                    'logo' => $league->logo ? asset($league->logo) : null,
                    'status' => $league->status,
                    'total_players' => $league->league_players_count,
                    'sold_players' => $league->leaguePlayers()->where('status', 'sold')->count(),
                    'current_player' => $currentData,
                ];
            });

        return response()->json([
            'success' => true,
            'leagues' => $leagues
        ]);
    }

    /**
     * Helper to trigger next random player
     */
    protected function triggerNextRandomPlayer(League $league)
    {
        // Try Available first
        $player = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'available')
            ->where('retention', false)
            ->inRandomOrder() // Random selection
            ->first();

        // Then Try Unsold
        if (!$player) {
            $player = LeaguePlayer::where('league_id', $league->id)
                ->where('status', 'unsold')
                ->where('retention', false)
                ->inRandomOrder()
                ->first();
        }

        if ($player) {
            $player->update([
                'status' => 'auctioning',
                'auctioned_at' => now(),
            ]);
            return $player->load(['player.position', 'player.primaryGameRole.gamePosition']);
        }

        return null;
    }

    public function startRandomPlayer(League $league)
    {
        $this->authorize('update', $league);

        // check if any player is already in auction
        $current = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->where('retention', false)
            ->first();

        if ($current) {
            return response()->json([
                'success' => true,
                'message' => 'A player is already in auction.',
                'data' => $current->load(['player.position', 'player.primaryGameRole.gamePosition'])
            ]);
        }

        $player = $this->triggerNextRandomPlayer($league);

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => 'No players remaining to auction.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $player
        ]);
    }

    /**
     * API: Get current auction state for a league (Current Player, Highest Bid).
     */
    public function getCurrentState(League $league)
    {
        $currentPlayer = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->where('retention', false)
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->first();

        $currentHighestBid = null;
        if ($currentPlayer) {
            $currentHighestBid = Auction::where('league_player_id', $currentPlayer->id)
                ->with(['leagueTeam.team'])
                ->latest('created_at')
                ->first();
        }

        $auctionStats = [
            'total_players' => $league->leaguePlayers()->count(),
            'sold_players' => $league->leaguePlayers()->where('status', 'sold')->count(),
            'unsold_players' => $league->leaguePlayers()->where('status', 'unsold')->count(),
        ];

        return response()->json([
            'success' => true,
            'league_id' => $league->id,
            'current_player' => $currentPlayer ? [
                'id' => $currentPlayer->id,
                'name' => $currentPlayer->player->name,
                'photo' => $currentPlayer->player->photo ? url(Storage::url($currentPlayer->player->photo)) : null,
                'position' => $currentPlayer->player->primaryGameRole->gamePosition->name ?? 'Player',
                'base_price' => $currentPlayer->base_price,
            ] : null,
            'current_bid' => $currentHighestBid ? [
                'amount' => $currentHighestBid->amount,
                'team_name' => $currentHighestBid->leagueTeam->team->name,
                'league_team_id' => $currentHighestBid->league_team_id,
                'team_logo' => $currentHighestBid->leagueTeam->team->logo ? url(Storage::url($currentHighestBid->leagueTeam->team->logo)) : null,
            ] : null,
            'stats' => $auctionStats,
        ]);
    }


    /**
     * API: Get recent sold players (replacing recent bids).
     */
    public function getRecentBids(League $league)
    {
        $soldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->whereIn('status', ['sold', 'retained'])
            ->whereNotNull('bid_price')
            ->with(['player', 'leagueTeam.team'])
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get()
                ->map(function ($lp) {
                return [
                    'amount' => $lp->bid_price,
                    'team_name' => $lp->leagueTeam->team->name ?? 'Unknown',
                    'team_logo' => $lp->leagueTeam->team->logo ? url(Storage::url($lp->leagueTeam->team->logo)) : null,
                    'player_name' => $lp->player->name,
                    'player_photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                    'time' => $lp->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'bids' => $soldPlayers
        ]);
    }

    public function getAvailablePlayers(League $league)
    {
        $availablePlayers = LeaguePlayer::where('league_id', $league->id)
            ->whereIn('status', ['available', 'unsold'])
            ->where('retention', false)
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($lp) {
                \Illuminate\Support\Facades\Log::info("Checking Player: " . $lp->id . " UserID: " . $lp->user_id);
                return [
                    'id' => $lp->id, // league_player_id
                    'user_id' => $lp->user_id, // player_id (user id)
                    'name' => $lp->player->name,
                    'photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                    'position' => $lp->player->primaryGameRole->gamePosition->name ?? $lp->player->position->name ?? 'Player',
                    'base_price' => $lp->base_price,
                ];
            });

        return response()->json([
            'success' => true,
            'players' => $availablePlayers
        ]);
    }

}
