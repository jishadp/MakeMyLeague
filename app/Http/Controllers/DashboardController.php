<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Models\GamePosition;
use App\Models\LeagueTeam;
use App\Models\LeaguePlayer;
use App\Models\Fixture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function view(Request $request)
    {
        $user = Auth::user();
        
        // ============ AVAILABLE LEAGUES TO JOIN (PRIMARY NEED) ============
        $searchQuery = $request->get('search');
        
        $availableLeagues = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })
        ->where('status', 'active')
        ->whereDate('end_date', '>=', now()->toDateString())
        ->when($searchQuery, function($query, $searchQuery) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                  ->orWhereHas('game', function($gameQuery) use ($searchQuery) {
                      $gameQuery->where('name', 'like', '%' . $searchQuery . '%');
                  })
                  ->orWhereHas('localBody', function($bodyQuery) use ($searchQuery) {
                      $bodyQuery->where('name', 'like', '%' . $searchQuery . '%');
                  });
            });
        })
        ->with(['game', 'localBody.district', 'leagueTeams', 'leaguePlayers'])
        ->withCount([
            'leagueTeams',
            'leaguePlayers',
            'leaguePlayers as user_registered_count' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            },
        ])
        ->latest()
        ->paginate(6)
        ->appends(['search' => $searchQuery]);

        // ============ USER'S CURRENT LEAGUE PARTICIPATIONS ============
        $userLeagueParticipations = LeaguePlayer::where('user_id', $user->id)
            ->with([
                'league.game',
                'league.localBody.district',
                'leagueTeam.team.localBody',
                'league.leagueTeams',
                'league.leaguePlayers'
            ])
        ->latest()
        ->get();

        // ============ GLOBAL PLAYER SPOTLIGHT (RANDOM SOLD PLAYERS) ============
        $playerSpotlight = LeaguePlayer::where('status', 'sold')
            ->with([
                'user.position',
                'league.game',
                'league.localBody.district',
                'leagueTeam.team.localBody',
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->take(40)
            ->get();
        $playerSpotlightRandom = $playerSpotlight->shuffle();

        // ============ USER'S AUCTION HISTORY ============
        $auctionHistory = LeaguePlayer::where('user_id', $user->id)
            ->where('status', 'sold')
            ->with([
                'league.game',
                'leagueTeam.team',
                'league.localBody.district'
            ])
            ->orderBy('bid_price', 'desc')
            ->get();

        // Calculate auction stats
        $auctionStats = [
            'total_value' => $auctionHistory->sum('bid_price'),
            'highest_bid' => $auctionHistory->max('bid_price'),
            'average_bid' => $auctionHistory->avg('bid_price'),
            'times_sold' => $auctionHistory->count(),
        ];

        // ============ UPCOMING MATCHES FOR USER'S TEAMS ============
        $upcomingMatches = Fixture::where(function($query) use ($user) {
            $query->whereHas('homeTeam.leaguePlayers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhereHas('awayTeam.leaguePlayers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })
        ->where('status', 'scheduled')
        ->where('match_date', '>=', now())
        ->with([
            'league.game',
            'homeTeam.team',
            'awayTeam.team',
            'leagueGroup'
        ])
        ->orderBy('match_date')
        ->orderBy('match_time')
        ->take(5)
        ->get();

        // ============ RECENT MATCH RESULTS ============
        $recentResults = Fixture::where(function($query) use ($user) {
            $query->whereHas('homeTeam.leaguePlayers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhereHas('awayTeam.leaguePlayers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })
        ->where('status', 'completed')
        ->with([
            'league.game',
            'homeTeam.team',
            'awayTeam.team',
            'leagueGroup'
        ])
        ->orderBy('match_date', 'desc')
        ->orderBy('match_time', 'desc')
        ->take(5)
        ->get();

        // ============ LIVE AUCTIONS ============
        // Show leagues where auction is currently active using the isAuctionActive() method
        $liveAuctions = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })
        ->with(['game', 'localBody.district', 'leaguePlayers'])
        ->where('auction_active', true)
        ->where('auction_started_at', '!=', null)
        ->where('auction_ended_at', null)
        ->get()
        ->filter(function($league) {
            // Use the isAuctionActive method to ensure consistency
            return $league->isAuctionActive();
        });

        // ============ TOP AUCTION LEADERBOARD (HIGHEST SOLD PLAYERS) ============
        $auctionLeaderboard = LeaguePlayer::where('status', 'sold')
            ->with([
                'user.position',
                'league.game',
                'leagueTeam.team'
            ])
            ->orderBy('bid_price', 'desc')
            ->take(10)
            ->get();

        // ============ PLAYER PROFILE INFO ============
        $playerInfo = null;
        if ($user->position_id) {
            $playerInfo = $user->load(['position', 'localBody.district']);
            
            // Add player statistics
            $playerInfo->stats = [
                'leagues_joined' => LeaguePlayer::where('user_id', $user->id)->count(),
                'leagues_active' => LeaguePlayer::where('user_id', $user->id)
                    ->whereHas('league', function($q) {
                        $q->where('status', 'active');
                    })->count(),
                'teams_played_for' => LeaguePlayer::where('user_id', $user->id)
                    ->distinct('league_team_id')
                    ->count('league_team_id'),
            ];
        }

        // ============ USER'S TEAMS (IF TEAM OWNER) ============
        $userOwnedTeams = $user->primaryOwnedTeams()->with([
            'localBody.district',
            'homeGround',
            'leagueTeams.league'
        ])->get();

        // ============ ORGANIZED LEAGUES (IF ORGANIZER) ============
        $organizedLeagues = collect();
        if ($user->isOrganizer()) {
            $organizedLeagues = $user->organizedLeagues()
                ->with([
                    'game',
                    'localBody.district',
                    'leagueTeams',
                    'leaguePlayers'
                ])
                ->withCount('leagueTeams', 'leaguePlayers')
                ->get();
        }

        // ============ RECENT ACTIVITIES / NOTIFICATIONS ============
        $recentActivities = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        // ============ QUICK STATS SUMMARY ============
        $quickStats = [
            'active_leagues' => League::where('status', 'active')->count(),
            'total_teams' => Team::count(),
            'players_registered' => User::count(),
        ];

        // ============ TRENDING LEAGUES (MOST POPULAR) ============
        $trendingLeagues = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })
        ->where('status', 'active')
        ->with(['game', 'localBody.district'])
        ->withCount('leagueTeams', 'leaguePlayers')
        ->orderBy('league_teams_count', 'desc')
        ->orderBy('league_players_count', 'desc')
        ->take(3)
        ->get();

        return view('dashboard', compact(
            'availableLeagues',
            'userLeagueParticipations',
            'auctionHistory',
            'auctionStats',
            'upcomingMatches',
            'recentResults',
            'liveAuctions',
            'auctionLeaderboard',
            'playerInfo',
            'userOwnedTeams',
            'organizedLeagues',
            'recentActivities',
            'quickStats',
            'trendingLeagues',
            'playerSpotlight',
            'playerSpotlightRandom',
        ));
    }

    /**
     * Display the auction listing page.
     */
    public function auctionsIndex()
    {
        // Get all leagues with auction access granted
        $allAuctionLeagues = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })->with(['game', 'leagueTeams.team', 'leaguePlayers'])
            ->where('auction_access_granted', true)
            ->get();

        // Categorize leagues based on auction status using isAuctionActive() method
        $liveAuctions = [];
        $pastAuctions = [];
        $upcomingAuctions = [];

        foreach ($allAuctionLeagues as $league) {
            // Use the isAuctionActive method for consistent status checking
            if ($league->isAuctionActive()) {
                // Live Auction: auction_active = true, auction_started_at is set, auction_ended_at is null
                $liveAuctions[] = $league;
            } elseif ($league->auction_ended_at) {
                // Past Auction: auction has ended
                $pastAuctions[] = $league;
            } else {
                // Upcoming Auction: auction not started yet
                $upcomingAuctions[] = $league;
            }
        }
        
        // Convert to collections for the view
        $liveAuctions = collect($liveAuctions);
        $upcomingAuctions = collect($upcomingAuctions);
        
        // Paginate past auctions manually
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $pastAuctionsCollection = collect($pastAuctions);
        $pastAuctions = new \Illuminate\Pagination\LengthAwarePaginator(
            $pastAuctionsCollection->forPage($currentPage, $perPage),
            $pastAuctionsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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
        // Only load league teams and the game, do not load all league players
        $league->load(['game', 'leagueTeams.team']);
        
        // Get only the current player being auctioned - do not fall back to available player
        $currentPlayer = \App\Models\LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->first();
            
        // We don't want to show any other players if there's no auctioning player
        // So we won't fall back to available players
            
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

        // Get all teams with their sold + retained players
        $teams = LeagueTeam::where('league_id', $league->id)
            ->with([
                'team',
                'auctioneer', // Include auctioneer information
                'teamAuctioneer.auctioneer', // Include active team auctioneer
                'leaguePlayers' => function($query) {
                    $query->with(['player.position', 'player.primaryGameRole.gamePosition'])
                          ->where(function ($q) {
                              $q->whereIn('status', ['retained', 'sold'])
                                ->orWhere('retention', true);
                          })
                          ->orderByRaw("FIELD(status, 'retained', 'sold')")
                          ->orderBy('bid_price', 'desc');
                }
            ])
            ->withCount(['leaguePlayers as sold_players_count' => function($query) {
                $query->where('status', 'sold');
            }])
            ->get();
        $availableBasePrices = \App\Models\LeaguePlayer::where('league_id', $league->id)
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
        $teams = $teams->map(function ($team) use ($availableBasePrices, $league, $retainedCounts, $auctionSlotsPerTeam) {
            $soldCount = $team->sold_players_count ?? 0;
            $retainedCount = $retainedCounts[$team->id] ?? 0;
            $playersNeeded = max($auctionSlotsPerTeam - $soldCount, 0);
            $reserveAmount = $playersNeeded > 0 ? $availableBasePrices->take($playersNeeded)->sum() : 0;
            $spentAmount = $team->leaguePlayers->sum('bid_price');
            $baseWallet = $league->team_wallet_limit ?? ($team->wallet_balance ?? 0);
            $availableWallet = max($baseWallet - $spentAmount, 0);
            $maxBidCap = max($availableWallet - $reserveAmount, 0);
            $team->players_needed = $playersNeeded;
            $team->reserve_amount = $reserveAmount;
            $team->max_bid_cap = $maxBidCap;
            $team->display_wallet = $availableWallet;
            $team->retained_players_count = $retainedCount;
            return $team;
        });

        return view('auction.live', compact('league', 'currentBids', 'currentPlayer', 'currentHighestBid', 'teams'));
    }
}
