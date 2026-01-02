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
    /**
     * Live matches hub for organizers/admins.
     */
    /**
     * Live matches hub for organizers/admins.
     */
    public function liveMatchesIndex(Request $request)
    {
        // 1. Get all leagues that have at least one fixture in relevant status
        // We'll group them by Game later in the collection
        // Eager load fixture count only
        $leagues = League::with(['game', 'localBody.district'])
            ->withCount(['fixtures as active_match_count' => function ($query) {
                $query->whereIn('status', ['in_progress', 'scheduled', 'unscheduled', 'completed']);
            }])
            ->whereHas('fixtures', function ($query) {
                // Include completed matches now
                $query->whereIn('status', ['in_progress', 'scheduled', 'unscheduled', 'completed']);
            })
            ->get();

        if ($leagues->isEmpty()) {
            return view('auction.live-matches', [
                'games' => collect(), 
                'leaguesByGame' => collect(), 
            ]);
        }

        // Group leagues by Game Name
        $leaguesByGame = $leagues->groupBy(function($league) {
            return $league->game ? $league->game->name : 'Other';
        });

        // Get unique games list for tabs
        $games = $leaguesByGame->keys();

        return view('auction.live-matches', compact('games', 'leaguesByGame'));
    }

    /**
     * Dedicated League Matches Page
     */
    public function leagueMatches(League $league)
    {
        $league->load(['game', 'localBody.district']);

        $fixtures = Fixture::with(['homeTeam.team', 'awayTeam.team', 'league', 'scorer', 'events' => function($q) {
                $q->latest();
            }])
            ->where('league_id', $league->id)
            ->whereIn('status', ['in_progress', 'scheduled', 'unscheduled', 'completed'])
            ->orderBy('match_date', 'desc')
            ->orderBy('match_time', 'desc')
            ->get();

        // Calculate standings from completed matches
        $completedMatches = $fixtures->where('status', 'completed');
        $standingsByGroup = [];

        // Fetch groups to properly label/organize
        $groups = $league->leagueGroups()->orderBy('sort_order')->get()->keyBy('id');
        
        // Initialize Default Group (for matches without group or if no groups exist)
        $defaultGroupId = 0;
        $standingsByGroup[$defaultGroupId] = [
            'group' => null,
            'teams' => []
        ];

        foreach ($groups as $group) {
            $standingsByGroup[$group->id] = [
                'group' => $group,
                'teams' => []
            ];
        }

        // We need to know which group a team belongs to, or use the fixture's context (Group Stage).
        // If it's a knockout match, it shouldn't count towards Group Standings typically?
        // User requirement: "points table only show groupt leadershei[ with respect groups"
        // So we should probably ONLY consider 'group_stage' matches for the points table.
        
        $groupStageMatches = $completedMatches->where('match_type', 'group_stage');

        // First, initialize all teams from scheduled/unscheduled group stage fixtures
        // This ensures teams appear in standings even before matches are played
        $allGroupStageFixtures = $fixtures->where('match_type', 'group_stage');
        
        foreach ($allGroupStageFixtures as $match) {
            $groupId = $match->league_group_id ?? $defaultGroupId;
            
            // Ensure group entry exists
            if (!isset($standingsByGroup[$groupId])) {
                $standingsByGroup[$groupId] = [
                    'group' => null,
                    'teams' => []
                ];
            }

            $homeTeamId = $match->home_team_id;
            $awayTeamId = $match->away_team_id;

            // Initialize team stats if not exists in this group
            foreach ([$homeTeamId, $awayTeamId] as $teamId) {
                if ($teamId && !isset($standingsByGroup[$groupId]['teams'][$teamId])) {
                    $standingsByGroup[$groupId]['teams'][$teamId] = [
                        'team_id' => $teamId,
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'goal_difference' => 0,
                        'points' => 0,
                    ];
                }
            }
        }

        // Now process completed matches to update stats
        foreach ($groupStageMatches as $match) {
            $groupId = $match->league_group_id ?? $defaultGroupId;
            
            // Ensure group entry exists (in case of null or deleted group)
            if (!isset($standingsByGroup[$groupId])) {
                 $standingsByGroup[$groupId] = [
                    'group' => null,
                    'teams' => []
                ];
            }

            $homeTeamId = $match->home_team_id;
            $awayTeamId = $match->away_team_id;
            $homeScore = $match->home_score ?? 0;
            $awayScore = $match->away_score ?? 0;

            // Initialize team stats if not exists in this group
            foreach ([$homeTeamId, $awayTeamId] as $teamId) {
                if (!isset($standingsByGroup[$groupId]['teams'][$teamId])) {
                    $standingsByGroup[$groupId]['teams'][$teamId] = [
                        'team_id' => $teamId,
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'goal_difference' => 0,
                        'points' => 0,
                    ];
                }
            }

            // Update home team stats
            $standingsByGroup[$groupId]['teams'][$homeTeamId]['played']++;
            $standingsByGroup[$groupId]['teams'][$homeTeamId]['goals_for'] += $homeScore;
            $standingsByGroup[$groupId]['teams'][$homeTeamId]['goals_against'] += $awayScore;

            // Update away team stats
            $standingsByGroup[$groupId]['teams'][$awayTeamId]['played']++;
            $standingsByGroup[$groupId]['teams'][$awayTeamId]['goals_for'] += $awayScore;
            $standingsByGroup[$groupId]['teams'][$awayTeamId]['goals_against'] += $homeScore;

            // Determine result
            if ($homeScore > $awayScore) {
                // Home win
                $standingsByGroup[$groupId]['teams'][$homeTeamId]['won']++;
                $standingsByGroup[$groupId]['teams'][$homeTeamId]['points'] += 3;
                $standingsByGroup[$groupId]['teams'][$awayTeamId]['lost']++;
            } elseif ($awayScore > $homeScore) {
                // Away win
                $standingsByGroup[$groupId]['teams'][$awayTeamId]['won']++;
                $standingsByGroup[$groupId]['teams'][$awayTeamId]['points'] += 3;
                $standingsByGroup[$groupId]['teams'][$homeTeamId]['lost']++;
            } else {
                // Draw
                $standingsByGroup[$groupId]['teams'][$homeTeamId]['drawn']++;
                $standingsByGroup[$groupId]['teams'][$homeTeamId]['points'] += 1;
                $standingsByGroup[$groupId]['teams'][$awayTeamId]['drawn']++;
                $standingsByGroup[$groupId]['teams'][$awayTeamId]['points'] += 1;
            }
        }

        // Calculate goal difference and attach team objects
        $leagueTeams = LeagueTeam::with('team')->where('league_id', $league->id)->get()->keyBy('id');

        foreach ($standingsByGroup as $groupId => &$groupData) {
            foreach ($groupData['teams'] as $teamId => &$stat) {
                $stat['goal_difference'] = $stat['goals_for'] - $stat['goals_against'];
                $stat['team'] = $leagueTeams[$teamId]->team ?? null;
            }
            // Sort each group
            usort($groupData['teams'], function($a, $b) {
                if ($b['points'] !== $a['points']) return $b['points'] - $a['points'];
                if ($b['goal_difference'] !== $a['goal_difference']) return $b['goal_difference'] - $a['goal_difference'];
                return $b['goals_for'] - $a['goals_for'];
            });
        }
        
        // Remove empty default group if unused
        if (empty($standingsByGroup[$defaultGroupId]['teams'])) {
            unset($standingsByGroup[$defaultGroupId]);
        }
        
        // Flatten if only one group? No, keep structure consistent for View.
        // Actually, existing view expects $standings array. I should pass a new variable or refactor view.
        // I will pass $standingsByGroup to view.
        
        // Note: I need to update the compact() call too.

        // Calculate Top Scorers (Goals) and Assist Leaders
        $fixtureIds = $fixtures->where('status', 'completed')->pluck('id');
        
        // Top Scorers - count GOAL events per player
        $topScorers = \App\Models\MatchEvent::whereIn('fixture_id', $fixtureIds)
            ->where('event_type', 'GOAL')
            ->whereNotNull('player_id')
            ->selectRaw('player_id, COUNT(*) as goals')
            ->groupBy('player_id')
            ->orderByDesc('goals')
            ->limit(10)
            ->with(['player.player', 'player.leagueTeam.team'])
            ->get()
            ->map(function ($item) {
                return [
                    'player' => $item->player?->player ?? null,
                    'team' => $item->player?->leagueTeam?->team ?? null,
                    'goals' => $item->goals,
                ];
            });

        // Top Assists - count events with assist_player_id
        $topAssists = \App\Models\MatchEvent::whereIn('fixture_id', $fixtureIds)
            ->where('event_type', 'GOAL')
            ->whereNotNull('assist_player_id')
            ->selectRaw('assist_player_id, COUNT(*) as assists')
            ->groupBy('assist_player_id')
            ->orderByDesc('assists')
            ->limit(10)
            ->get();

        // Fetch player details for assists
        $assistPlayerIds = $topAssists->pluck('assist_player_id');
        $assistPlayers = \App\Models\LeaguePlayer::with(['player', 'leagueTeam.team'])
            ->whereIn('id', $assistPlayerIds)
            ->get()
            ->keyBy('id');

        $topAssists = $topAssists->map(function ($item) use ($assistPlayers) {
            $lp = $assistPlayers[$item->assist_player_id] ?? null;
            return [
                'player' => $lp?->player ?? null,
                'team' => $lp?->leagueTeam?->team ?? null,
                'assists' => $item->assists,
            ];
        });

        // Yellow Cards - count YELLOW_CARD events per player
        $topYellowCards = \App\Models\MatchEvent::whereIn('fixture_id', $fixtureIds)
            ->where('event_type', 'YELLOW_CARD')
            ->whereNotNull('player_id')
            ->selectRaw('player_id, COUNT(*) as cards')
            ->groupBy('player_id')
            ->orderByDesc('cards')
            ->limit(10)
            ->get();

        $yellowPlayerIds = $topYellowCards->pluck('player_id');
        $yellowPlayers = \App\Models\LeaguePlayer::with(['player', 'leagueTeam.team'])
            ->whereIn('id', $yellowPlayerIds)
            ->get()
            ->keyBy('id');

        $topYellowCards = $topYellowCards->map(function ($item) use ($yellowPlayers) {
            $lp = $yellowPlayers[$item->player_id] ?? null;
            return [
                'player' => $lp?->player ?? null,
                'team' => $lp?->leagueTeam?->team ?? null,
                'cards' => $item->cards,
            ];
        });

        // Red Cards - count RED_CARD events per player
        $topRedCards = \App\Models\MatchEvent::whereIn('fixture_id', $fixtureIds)
            ->where('event_type', 'RED_CARD')
            ->whereNotNull('player_id')
            ->selectRaw('player_id, COUNT(*) as cards')
            ->groupBy('player_id')
            ->orderByDesc('cards')
            ->limit(10)
            ->get();

        $redPlayerIds = $topRedCards->pluck('player_id');
        $redPlayers = \App\Models\LeaguePlayer::with(['player', 'leagueTeam.team'])
            ->whereIn('id', $redPlayerIds)
            ->get()
            ->keyBy('id');

        $topRedCards = $topRedCards->map(function ($item) use ($redPlayers) {
            $lp = $redPlayers[$item->player_id] ?? null;
            return [
                'player' => $lp?->player ?? null,
                'team' => $lp?->leagueTeam?->team ?? null,
                'cards' => $item->cards,
            ];
        });

        return view('auction.league-matches', compact('league', 'fixtures', 'standingsByGroup', 'topScorers', 'topAssists', 'topYellowCards', 'topRedCards'));
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
            
        $categories = $league->playerCategories()->get();
            
        return view('auction.index', compact('leaguePlayers', 'league', 'currentPlayer', 'currentHighestBid', 'teams', 'userAuctioneerAssignment', 'userRole', 'userTeamId', 'categories'));
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
        // For reserve calculation, use available players first, fall back to unsold if none available
        // Exclude retained players as they are already assigned to teams
        $availableBasePrices = $league->leaguePlayers()
            ->where('status', 'available')
            ->where('retention', false)
            ->orderBy('base_price')
            ->pluck('base_price')
            ->map(fn ($price) => max((float) $price, 0))
            ->values();
        
        // If no available players, use unsold players for reserve calculation
        if ($availableBasePrices->isEmpty()) {
            $availableBasePrices = $league->leaguePlayers()
                ->where('status', 'unsold')
                ->where('retention', false)
                ->orderBy('base_price')
                ->pluck('base_price')
                ->map(fn ($price) => max((float) $price, 0))
                ->values();
        }
        
        // If still empty or all zeros, use the league's default base price as fallback
        if ($availableBasePrices->isEmpty() || $availableBasePrices->sum() == 0) {
            $defaultBasePrice = $league->default_base_price ?? 100;
            $totalAvailableCount = $league->leaguePlayers()
                ->whereIn('status', ['available', 'unsold'])
                ->where('retention', false)
                ->count();
            // Create a collection with the default base price for each available slot
            $availableBasePrices = collect(array_fill(0, max($totalAvailableCount, 20), $defaultBasePrice));
        }

        $categories = $league->playerCategories()->get();
        $categoryCounts = \App\Models\LeaguePlayer::where('league_id', $league->id)
            ->whereIn('status', ['sold', 'retained'])
            ->whereNotNull('league_player_category_id')
            ->select('league_team_id', 'league_player_category_id', DB::raw('count(*) as count'))
            ->groupBy('league_team_id', 'league_player_category_id')
            ->get()
            ->groupBy('league_team_id');
            
        $retainedCounts = \App\Models\LeaguePlayer::where('league_id', $league->id)

            ->where('retention', true)
            ->groupBy('league_team_id')
            ->select('league_team_id', DB::raw('count(*) as count'))
            ->pluck('count', 'league_team_id');
        $auctionSlotsPerTeam = max(($league->max_team_players ?? 0) - ($league->retention_players ?? 0), 0);
        $teams = $teams->map(function ($team) use ($availableBasePrices, $auctionSlotsPerTeam, $league, $retainedCounts, $categoryCounts, $categories) {
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

            $teamStats = $categoryCounts[$team->id] ?? collect();
            $team->category_compliance = $categories->map(function($cat) use ($teamStats) {
                $count = $teamStats->where('league_player_category_id', $cat->id)->first()->count ?? 0;
                return [
                    'name' => $cat->name,
                    'min' => $cat->min_requirement,
                    'max' => $cat->max_requirement,
                    'current' => $count,
                    'met' => $count >= $cat->min_requirement,
                    'exceeded' => $cat->max_requirement && $count > $cat->max_requirement,
                ];
            });

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
            'categories' => $categories,
        ]);
    }

    /**
     * API: Get control room data for mobile app
     */
    public function getControlRoomData(League $league)
    {
        $user = auth()->user();

        if (!$user || !$user->canManageLeague($league->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Only league organizers or admins can access the auction control room.'
            ], 403);
        }

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

        $availablePlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'available')
            ->where('retention', false)
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->orderBy('updated_at', 'asc')
            ->get();
            
        $unsoldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'unsold')
            ->where('retention', false)
            ->with(['player.position', 'player.primaryGameRole.gamePosition'])
            ->orderBy('updated_at', 'asc')
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
        
        $teams = $teams->map(function ($team) use ($availableBasePrices, $auctionSlotsPerTeam, $league, $retainedCounts, $currentPlayer) {
            $retainedCount = $retainedCounts[$team->id] ?? 0;
            $playersNeeded = max($auctionSlotsPerTeam - ($team->sold_players_count ?? 0), 0);
            $futureSlots = max($playersNeeded - 1, 0);
            $reserveAmount = $futureSlots > 0 ? $availableBasePrices->take($futureSlots)->sum() : 0;
            $baseWallet = $league->team_wallet_limit ?? ($team->wallet_balance ?? 0);
            $spentAmount = (float) ($team->spent_amount ?? 0);
            $availableWallet = max($baseWallet - $spentAmount, 0);
            $maxBidCap = max($availableWallet - $reserveAmount, 0);
            
            $currentBidAmount = $currentPlayer ? ($currentPlayer->base_price ?? 0) : 0;
            if ($currentPlayer && $currentPlayer->auctionBids()->exists()) {
                $latestBid = $currentPlayer->auctionBids()->latest('created_at')->first();
                if ($latestBid) {
                    $currentBidAmount = $latestBid->amount;
                }
            }
            
            $teamDisabled = ($playersNeeded === 0) || ($currentPlayer && $maxBidCap < $currentBidAmount);
            
            return [
                'id' => $team->id,
                'name' => $team->team->name ?? 'Team #' . $team->id,
                'logo' => $team->team->logo ? url(Storage::url($team->team->logo)) : null,
                'wallet_balance' => $availableWallet,
                'players_needed' => $playersNeeded,
                'reserve_amount' => $reserveAmount,
                'max_bid_cap' => $maxBidCap,
                'retained_players_count' => $retainedCount,
                'sold_players_count' => $team->sold_players_count ?? 0,
                'disabled' => $teamDisabled,
            ];
        });

        $auctionStats = [
            'total_players' => $league->leaguePlayers()->count(),
            'sold_players' => $league->leaguePlayers()->where('status', 'sold')->count(),
            'available_players' => $league->leaguePlayers()->where('status', 'available')->count(),
            'unsold_players' => $league->leaguePlayers()->where('status', 'unsold')->count(),
            'wallet_spent' => $league->leaguePlayers()->where('status', 'sold')->sum('bid_price'),
        ];

        $bidIncrements = $league->bid_increment_type === 'predefined' && !empty($league->predefined_increments)
            ? $league->predefined_increments
            : [['min' => 0, 'max' => 1000, 'increment' => 50], ['min' => 1000, 'max' => null, 'increment' => 100]];

        return response()->json([
            'success' => true,
            'league' => [
                'id' => $league->id,
                'name' => $league->name,
                'slug' => $league->slug,
                'status' => $league->status,
                'season' => $league->season,
                'game_name' => $league->game->name ?? 'Game TBA',
                'teams_count' => $league->league_teams_count,
            ],
            'current_player' => $currentPlayer ? [
                'id' => $currentPlayer->id,
                'player_id' => $currentPlayer->player->id,
                'name' => $currentPlayer->player->name,
                'photo' => $currentPlayer->player->photo ? url(Storage::url($currentPlayer->player->photo)) : null,
                'role' => $currentPlayer->player->primaryGameRole->gamePosition->name ?? 
                         $currentPlayer->player->position->name ?? 'Role TBA',
                'base_price' => $currentPlayer->base_price,
            ] : null,
            'current_highest_bid' => $currentHighestBid ? [
                'amount' => $currentHighestBid->amount,
                'league_team_id' => $currentHighestBid->league_team_id,
                'team_name' => $currentHighestBid->leagueTeam->team->name ?? 'Unknown',
            ] : null,
            'available_players' => $availablePlayers->map(function ($lp) {
                return [
                    'id' => $lp->id,
                    'user_id' => $lp->user_id,
                    'name' => $lp->player->name,
                    'role' => $lp->player->primaryGameRole->gamePosition->name ?? 
                             $lp->player->position->name ?? '',
                    'base_price' => $lp->base_price,
                    'photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                ];
            })->values(),
            'unsold_players' => $unsoldPlayers->map(function ($lp) {
                return [
                    'id' => $lp->id,
                    'user_id' => $lp->user_id,
                    'name' => $lp->player->name,
                    'role' => $lp->player->primaryGameRole->gamePosition->name ?? 
                             $lp->player->position->name ?? '',
                    'base_price' => $lp->base_price,
                    'photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                ];
            })->values(),
            'teams' => $teams->values(),
            'bid_increments' => $bidIncrements,
            'auction_stats' => $auctionStats,
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

        // Check if team has sufficient balance (including any refundable amount from previous bid)
        $availableBalance = $bidTeam->wallet_balance + $refundableAmount;
        if ($availableBalance < $newBid) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient team balance. Available: ₹' . number_format($availableBalance) . ', Required: ₹' . number_format($newBid)
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
            // Always use the full remaining balance for the final player slot
            // This ensures teams spend all their money on the last player
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
            // Get all bids for this player
            $bids = Auction::where('league_player_id', $leaguePlayer->id)->orderBy('id')->get();
            
            // Get the LATEST (current/winning) bid for the winning team
            $winningBid = $bids->where('league_team_id', $team->id)->sortByDesc('id')->first();

            // Refund all non-winning bids that haven't been refunded yet
            // (In normal flow, these should already be refunded by the call() method, but we ensure it here for safety)
            foreach ($bids as $bid) {
                if ($bid->status === 'refunded') {
                    // Already refunded, skip
                    continue;
                }

                if ($bid->league_team_id != $team->id) {
                    // This is a non-winning bid, refund it
                    LeagueTeam::where('id', $bid->league_team_id)->increment('wallet_balance', $bid->amount);
                    $bid->update(['status' => 'refunded']);
                }
            }
            
            // Mark the winning bid
            if ($winningBid) {
                $winningBid->update(['status' => 'won']);
            }
            
            // Calculate the amount that was already deducted from the winning team's wallet
            // This is the amount of the winning bid (if it exists)
            $alreadyDeducted = $winningBid ? (float) $winningBid->amount : 0;
            
            // Calculate the adjustment needed for the final amount
            // balanceAdjustment = alreadyDeducted - finalAmount
            // If positive: refund the difference (finalAmount is less than bid)
            // If negative: deduct more (finalAmount is more than bid)
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
        
        // For reserve calculation, use available players first, fall back to unsold if none available
        // Exclude retained players as they are already assigned to teams
        $availableBasePrices = $league->leaguePlayers()
            ->where('status', 'available')
            ->where('retention', false)
            ->orderBy('base_price')
            ->pluck('base_price')
            ->map(fn ($price) => max((float) $price, 0))
            ->values();
        
        // If no available players, use unsold players for reserve calculation
        if ($availableBasePrices->isEmpty()) {
            $availableBasePrices = $league->leaguePlayers()
                ->where('status', 'unsold')
                ->where('retention', false)
                ->orderBy('base_price')
                ->pluck('base_price')
                ->map(fn ($price) => max((float) $price, 0))
                ->values();
        }
        
        // If still empty or all zeros, use the league's default base price as fallback
        if ($availableBasePrices->isEmpty() || $availableBasePrices->sum() == 0) {
            $defaultBasePrice = $league->default_base_price ?? 100;
            $totalAvailableCount = $league->leaguePlayers()
                ->whereIn('status', ['available', 'unsold'])
                ->where('retention', false)
                ->count();
            $availableBasePrices = collect(array_fill(0, max($totalAvailableCount, 20), $defaultBasePrice));
        }

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
        $user = auth()->user();
        
        // For now, return leagues that are 'active' or have a status indicating auction in progress.
        $leagues = League::whereIn('status', ['active', 'auction'])
            ->withCount('leaguePlayers')
            ->with(['leaguePlayers' => function ($query) {
                $query->where('status', 'auctioning')
                      ->with(['player', 'auctionBids' => function ($q) {
                          $q->orderBy('amount', 'desc')->limit(1)->with('leagueTeam.team');
                      }]);
            }])
            ->get()
            ->map(function ($league) use ($user) {
                $currentPlayer = $league->leaguePlayers->first();
                $currentData = null;

                if ($currentPlayer && $currentPlayer->player) {
                    $highestBid = $currentPlayer->auctionBids->first();
                    $teamName = 'Base Price';
                    if ($highestBid && $highestBid->leagueTeam && $highestBid->leagueTeam->team) {
                        $teamName = $highestBid->leagueTeam->team->name;
                    } elseif ($highestBid) {
                         $teamName = 'Bid placed'; 
                    }

                    $currentData = [
                        'name' => $currentPlayer->player->name ?? 'Unknown',
                        'bid' => $highestBid ? $highestBid->amount : $currentPlayer->base_price,
                        'team' => $teamName,
                        'photo' => $currentPlayer->player->profile_photo_path ? asset('storage/' . $currentPlayer->player->profile_photo_path) : null,
                    ];
                }

                // Check if user can manage this league
                $canManage = $user && $user->canManageLeague($league->id);

                return [
                    'id' => $league->id,
                    'name' => $league->name,
                    'slug' => $league->slug,
                    'logo' => $league->logo ? asset($league->logo) : null,
                    'status' => $league->status,
                    'total_players' => $league->league_players_count,
                    'sold_players' => $league->leaguePlayers()->where('status', 'sold')->count(),
                    'current_player' => $currentData,
                    'can_manage' => $canManage,
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
                'team_id' => $currentHighestBid->league_team_id,
                'team_name' => $currentHighestBid->leagueTeam->team->name,
            ] : null,
            'bid_rules' => [
                'type' => $league->bid_increment_type,
                'custom_increment' => $league->custom_bid_increment,
                'rules' => $league->predefined_increments ?? [
                    ['min' => 0, 'max' => 100, 'increment' => 5],
                    ['min' => 101, 'max' => 500, 'increment' => 10],
                    ['min' => 501, 'max' => 1000, 'increment' => 25],
                    ['min' => 1001, 'max' => null, 'increment' => 50],
                ]
            ],

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

    /**
     * API: Update bid increment rules.
     */
    public function updateBidRules(Request $request, League $league)
    {
        $request->validate([
            'type' => 'required|in:predefined,custom',
            'custom_increment' => 'nullable|numeric|min:1',
            'rules' => 'nullable|array',
            'rules.*.min' => 'required_with:rules|numeric|min:0',
            'rules.*.max' => 'nullable|numeric|gt:rules.*.min',
            'rules.*.increment' => 'required_with:rules|numeric|min:1',
        ]);

        $league->update([
            'bid_increment_type' => $request->type,
            'custom_bid_increment' => $request->custom_increment,
            'predefined_increments' => $request->rules,
        ]);



        return response()->json([
            'success' => true,
            'message' => 'Bid rules updated successfully',
            'rules' => [
                'type' => $league->bid_increment_type,
                'custom_increment' => $league->custom_bid_increment,
                'rules' => $league->predefined_increments,
            ]
        ]);
    }

    public function toggleCategoryRules(Request $request, League $league)
    {
        $request->validate(['enabled' => 'required|boolean']);
        $league->update(['auction_category_rules_enabled' => $request->enabled]);
        return response()->json(['success' => true, 'message' => 'Category rules updated.']);
    }

    /**
     * API: Get sold players for a league ordered by time
     */
    public function getSoldPlayers(League $league)
    {
        $soldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'sold')
            ->whereNotNull('bid_price')
            ->with(['player', 'leagueTeam.team'])
            ->orderBy('updated_at', 'asc') // Order by sold time (earliest first)
            ->get()
            ->map(function ($lp) {
                return [
                    'id' => $lp->id,
                    'name' => $lp->player->name ?? 'Unknown',
                    'photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                    'role' => $lp->player->primaryGameRole->gamePosition->name ?? $lp->player->position->name ?? 'N/A',
                    'team_name' => $lp->leagueTeam->team->name ?? 'Unknown',
                    'team_logo' => $lp->leagueTeam->team->logo ? url(Storage::url($lp->leagueTeam->team->logo)) : null,
                    'final_amount' => $lp->bid_price,
                    'sold_at' => $lp->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'sold_players' => $soldPlayers
        ]);
    }

    /**
     * API: Download sold players as PDF
     */
    public function downloadSoldPlayersPDF(League $league)
    {
        $soldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'sold')
            ->whereNotNull('bid_price')
            ->with(['player', 'leagueTeam.team'])
            ->orderBy('updated_at', 'asc')
            ->get();

        // Using dompdf library
        $pdf = \PDF::loadView('auction.sold-players-pdf', [
            'league' => $league,
            'soldPlayers' => $soldPlayers
        ]);

        return $pdf->download('sold-players-' . $league->slug . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * API: Download sold players as CSV
     */
    public function downloadSoldPlayersCSV(League $league)
    {
        $soldPlayers = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'sold')
            ->whereNotNull('bid_price')
            ->with(['player', 'leagueTeam.team'])
            ->orderBy('updated_at', 'asc')
            ->get();

        $filename = 'sold-players-' . $league->slug . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($soldPlayers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['SL', 'Player Name', 'Role', 'Team', 'Amount', 'Sold Time']);

            // Add data rows
            foreach ($soldPlayers as $index => $player) {
                fputcsv($file, [
                    $index + 1,
                    $player->player->name ?? 'Unknown',
                    $player->player->primaryGameRole->gamePosition->name ?? $player->player->position->name ?? 'N/A',
                    $player->leagueTeam->team->name ?? 'Unknown',
                    $player->bid_price ?? 0,
                    $player->updated_at->format('M d, Y h:i A')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Update a sold player's bid price and adjust team wallet accordingly.
     */
    public function updatePlayerBidPrice(Request $request)
    {
        $validated = $request->validate([
            'league_player_id' => ['required', 'integer', 'exists:league_players,id'],
            'new_bid_price' => ['required', 'numeric', 'min:0'],
        ]);

        $leaguePlayer = LeaguePlayer::with(['league', 'leagueTeam'])->find($validated['league_player_id']);
        
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if ($leaguePlayer->status !== 'sold') {
            return response()->json(['success' => false, 'message' => 'Only sold players can have their bid price updated.'], 422);
        }

        if (!$leaguePlayer->leagueTeam) {
            return response()->json(['success' => false, 'message' => 'Player is not assigned to a team.'], 422);
        }

        $this->authorize('markSoldUnsold', $leaguePlayer->league);

        $oldBidPrice = (float) ($leaguePlayer->bid_price ?? 0);
        $newBidPrice = (float) $validated['new_bid_price'];
        $priceDifference = $oldBidPrice - $newBidPrice;

        $team = $leaguePlayer->leagueTeam;

        // Log the action
        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_bid_price_updated',
            'LeaguePlayer',
            $leaguePlayer->id,
            [
                'old_bid_price' => $oldBidPrice,
                'new_bid_price' => $newBidPrice,
                'team_id' => $team->id,
                'wallet_adjustment' => $priceDifference
            ]
        );

        DB::transaction(function () use ($leaguePlayer, $newBidPrice, $team, $priceDifference) {
            // Update player's bid_price
            $leaguePlayer->update(['bid_price' => $newBidPrice]);
            
            // Adjust team wallet: positive difference = refund, negative = extra deduction
            if ($priceDifference != 0) {
                $team->increment('wallet_balance', $priceDifference);
            }
        });

        // Refresh models to get updated values
        $leaguePlayer->refresh();
        $team->refresh();

        // Calculate new team totals
        $soldPlayers = $team->leaguePlayers()->where('status', 'sold')->get();
        $totalSpent = $soldPlayers->sum('bid_price');

        return response()->json([
            'success' => true,
            'message' => 'Player bid price updated successfully.',
            'player' => [
                'id' => $leaguePlayer->id,
                'name' => $leaguePlayer->player->name ?? 'Unknown',
                'new_bid_price' => $newBidPrice,
            ],
            'team' => [
                'id' => $team->id,
                'name' => $team->team->name ?? 'Unknown',
                'wallet_balance' => $team->wallet_balance,
                'total_spent' => $totalSpent,
                'player_count' => $soldPlayers->count(),
            ],
            'adjustment' => $priceDifference,
        ]);
    }

    /**
     * Show the player management page for organizers.
     */
    public function managePlayers(League $league)
    {
        $user = auth()->user();

        if (!$user || !$user->canManageLeague($league->id)) {
            abort(403, 'Only league organizers or admins can access player management.');
        }

        $this->authorize('viewAuctionPanel', $league);

        $league->load(['game', 'localBody.district']);

        // Get counts for each status
        $statusCounts = [
            'all' => LeaguePlayer::where('league_id', $league->id)->where('retention', false)->count(),
            'available' => LeaguePlayer::where('league_id', $league->id)->where('status', 'available')->where('retention', false)->count(),
            'sold' => LeaguePlayer::where('league_id', $league->id)->where('status', 'sold')->where('retention', false)->count(),
            'unsold' => LeaguePlayer::where('league_id', $league->id)->where('status', 'unsold')->where('retention', false)->count(),
        ];

        // Get all teams with their sold players and statistics (similar to control room)
        $teams = LeagueTeam::where('league_id', $league->id)
            ->with(['team'])
            ->get()
            ->map(function ($leagueTeam) {
                $soldPlayers = $leagueTeam->leaguePlayers()->where('status', 'sold')->with('player')->get();
                $soldCount = $soldPlayers->count();
                $totalSpent = $soldPlayers->sum('bid_price');
                $walletBalance = $leagueTeam->wallet_balance ?? 0;

                return [
                    'id' => $leagueTeam->id,
                    'team_id' => $leagueTeam->team_id,
                    'name' => $leagueTeam->team->name,
                    'logo' => $leagueTeam->team->logo ? Storage::url($leagueTeam->team->logo) : null,
                    'sold_count' => $soldCount,
                    'total_spent' => $totalSpent,
                    'wallet_balance' => $walletBalance,
                    'sold_players' => $soldPlayers->map(function ($lp) {
                        return [
                            'id' => $lp->id,
                            'name' => $lp->player->name ?? 'Unknown',
                            'role' => $lp->player->primaryGameRole->gamePosition->name ?? $lp->player->position->name ?? 'Player',
                            'photo' => $lp->player->photo ? Storage::url($lp->player->photo) : null,
                            'bid_price' => $lp->bid_price,
                        ];
                    }),
                ];
            });

        $categories = $league->playerCategories()->get();
        $districts = \App\Models\District::select('id', 'name')->orderBy('name')->get();

        return view('auction.manage-players', compact('league', 'statusCounts', 'teams', 'user', 'categories', 'districts'));
    }

    /**
     * API: Get all league players with optional status filter
     */
    public function getAllLeaguePlayers(Request $request, League $league)
    {
        $status = $request->input('status'); // available, sold, unsold, or null for all

        $query = LeaguePlayer::where('league_id', $league->id)
            ->where('retention', false)
            ->with(['player.primaryGameRole.gamePosition', 'player.position', 'leagueTeam.team', 'category']);

        if ($status && in_array($status, ['available', 'sold', 'unsold'])) {
            $query->where('status', $status);
        }

        $players = $query->orderByRaw("CASE WHEN status = 'auctioning' THEN 0 ELSE 1 END")
            ->latest('updated_at')
            ->get()
            ->map(function ($lp) {
                return [
                    'id' => $lp->id,
                    'user_id' => $lp->user_id,
                    'name' => $lp->player->name ?? 'Unknown',
                    'photo' => $lp->player->photo ? url(Storage::url($lp->player->photo)) : null,
                    'role' => $lp->player->primaryGameRole->gamePosition->name ?? $lp->player->position->name ?? 'N/A',
                    'status' => $lp->status,
                    'base_price' => $lp->base_price,
                    'bid_price' => $lp->bid_price,
                    'team_id' => $lp->league_team_id,
                    'team_name' => $lp->leagueTeam->team->name ?? null,
                    'team_logo' => $lp->leagueTeam && $lp->leagueTeam->team->logo 
                        ? url(Storage::url($lp->leagueTeam->team->logo)) 
                        : null,
                    'category_id' => $lp->league_player_category_id,
                    'category_name' => $lp->category->name ?? null,
                    'updated_at' => $lp->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'players' => $players,
            'counts' => [
                'all' => LeaguePlayer::where('league_id', $league->id)->where('retention', false)->count(),
                'available' => LeaguePlayer::where('league_id', $league->id)->where('status', 'available')->where('retention', false)->count(),
                'sold' => LeaguePlayer::where('league_id', $league->id)->where('status', 'sold')->where('retention', false)->count(),
                'unsold' => LeaguePlayer::where('league_id', $league->id)->where('status', 'unsold')->where('retention', false)->count(),
            ]
        ]);
    }

    /**
     * API: Revert a player's status to available
     * For sold players: refunds bid_price to team wallet
     * For unsold players: simply changes status
     */
    public function revertPlayerToAvailable(Request $request)
    {
        $validated = $request->validate([
            'league_player_id' => ['required', 'integer', 'exists:league_players,id'],
        ]);

        $leaguePlayer = LeaguePlayer::with(['league', 'leagueTeam.team', 'player'])->find($validated['league_player_id']);

        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if (!in_array($leaguePlayer->status, ['sold', 'unsold'])) {
            return response()->json([
                'success' => false, 
                'message' => 'Only sold or unsold players can be reverted to available.'
            ], 422);
        }

        $this->authorize('markSoldUnsold', $leaguePlayer->league);

        $previousStatus = $leaguePlayer->status;
        $refundAmount = 0;
        $teamName = null;

        DB::transaction(function () use ($leaguePlayer, $previousStatus, &$refundAmount, &$teamName) {
            if ($previousStatus === 'sold' && $leaguePlayer->leagueTeam) {
                $team = $leaguePlayer->leagueTeam;
                $teamName = $team->team->name ?? 'Unknown';
                $refundAmount = (float) ($leaguePlayer->bid_price ?? 0);

                // Refund the bid amount to team's wallet
                if ($refundAmount > 0) {
                    $team->increment('wallet_balance', $refundAmount);
                }

                // Clear team assignment and bid price
                $leaguePlayer->update([
                    'status' => 'available',
                    'league_team_id' => null,
                    'bid_price' => null,
                ]);
            } else {
                // For unsold players, just change status
                $leaguePlayer->update([
                    'status' => 'available',
                ]);
            }
        });

        // Log the action
        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_reverted_to_available',
            'LeaguePlayer',
            $leaguePlayer->id,
            [
                'previous_status' => $previousStatus,
                'refund_amount' => $refundAmount,
                'team_name' => $teamName,
            ]
        );

        $leaguePlayer->refresh();

        return response()->json([
            'success' => true,
            'message' => $previousStatus === 'sold' 
                ? "Player reverted to available. ₹" . number_format($refundAmount) . " refunded to {$teamName}."
                : 'Player status changed to available.',
            'player' => [
                'id' => $leaguePlayer->id,
                'name' => $leaguePlayer->player->name ?? 'Unknown',
                'status' => $leaguePlayer->status,
            ],
            'refund' => [
                'amount' => $refundAmount,
                'team' => $teamName,
            ],
        ]);
    }



    /**
     * API: Search for users not in the league to replace an existing player.
     */
    public function searchReplaceablePlayers(Request $request, League $league)
    {
        $query = $request->input('query');
        if (strlen($query) < 2) {
            return response()->json(['success' => true, 'players' => []]);
        }

        // Get IDs of users already in this league
        $existingUserIds = LeaguePlayer::where('league_id', $league->id)
            ->pluck('user_id')
            ->toArray();

        // Search users
        $users = \App\Models\User::with(['primaryGameRole.gamePosition'])
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('mobile', 'LIKE', "%{$query}%");
            })
            ->whereNotIn('id', $existingUserIds)
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'photo' => $user->photo ? Storage::url($user->photo) : null,
                    'role' => $user->primaryGameRole?->gamePosition?->name ?? 'Player',
                ];
            });

        return response()->json(['success' => true, 'players' => $users]);
    }

    /**
     * API: Replace an available player with a new user.
     */
    public function replaceLeaguePlayer(Request $request)
    {
        $validated = $request->validate([
            'league_player_id' => ['required', 'integer', 'exists:league_players,id'],
            'new_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $leaguePlayer = LeaguePlayer::with(['league', 'player'])->find($validated['league_player_id']);
        
        if (!$leaguePlayer) {
            return response()->json(['success' => false, 'message' => 'Player not found.'], 404);
        }

        if ($leaguePlayer->status !== 'available') {
            return response()->json(['success' => false, 'message' => 'Only available players can be replaced.'], 422);
        }

        // Verify new user is not already in the league
        $exists = LeaguePlayer::where('league_id', $leaguePlayer->league_id)
            ->where('user_id', $validated['new_user_id'])
            ->exists();
            
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'The selected player is already in this league.'], 422);
        }

        $this->authorize('viewAuctionPanel', $leaguePlayer->league);

        $oldPlayerName = $leaguePlayer->player->name;
        $newUser = \App\Models\User::find($validated['new_user_id']);

        // Update the player
        $leaguePlayer->update([
            'user_id' => $validated['new_user_id'],
            // Optionally reset slug to regenerate on next save if we clear it, or update manually
            // 'slug' => null // Depending on slug strategy. Let's force update slug if we can, or just leave it.
            // Actually, LeaguePlayer slug is generated on create. If we want it updated, we might need to handle it. 
            // But preserving slug might be safer for existing links, unless links use ID.
            // The user requirements didn't specify slug updates, but it's cleaner.
        ]);

        // Log the action
        \App\Models\AuctionLog::logAction(
            $leaguePlayer->league_id,
            auth()->id(),
            'player_replaced',
            'LeaguePlayer',
            $leaguePlayer->id,
            [
                'old_user_id' => $validated['league_player_id'], // actually we want the old user id from before update, but we lost it.
                // It's okay, we authenticated user logged it.
                'old_player_name' => $oldPlayerName,
                'new_user_id' => $newUser->id,
                'new_player_name' => $newUser->name,
            ]
        );

        return response()->json([
            'success' => true, 
            'message' => "Player replaced successfully. $oldPlayerName → {$newUser->name}",
        ]);
    }

    public function updateCategoryRules(Request $request, League $league)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'categories' => 'present|array',
            'categories.*.id' => 'required|exists:league_player_categories,id',
            'categories.*.min_requirement' => 'nullable|integer|min:0',
            'categories.*.max_requirement' => 'nullable|integer|min:0',
        ]);

        // Toggle global setting
        $league->update(['auction_category_rules_enabled' => $validated['enabled']]);

        // Update each category
        foreach ($validated['categories'] as $catData) {
            $category = \App\Models\LeaguePlayerCategory::where('league_id', $league->id)
                ->where('id', $catData['id'])
                ->first();

            if ($category) {
                $category->update([
                    'min_requirement' => $catData['min_requirement'] ?? 0,
                    'max_requirement' => $catData['max_requirement'] ?? null,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Category rules updated successfully.']);
    }

}
