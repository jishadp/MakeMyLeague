<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveAuctionDataService
{
    public function buildPayload(League $league): array
    {
        $league->load(['game', 'leagueTeams.team']);

        $currentPlayer = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'auctioning')
            ->with([
                'player.position',
                'player.primaryGameRole.gamePosition',
                'player.localBody.district.state',
            ])
            ->first();

        $currentHighestBid = null;
        if ($currentPlayer) {
            $currentHighestBid = Auction::where('league_player_id', $currentPlayer->id)
                ->with(['leagueTeam.team'])
                ->latest('created_at')
                ->first();
        }

        $currentBids = Auction::with(['leagueTeam.team', 'leaguePlayer.user'])
            ->whereHas('leaguePlayer', function ($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->latest()
            ->get()
            ->groupBy('league_player_id');

        $teams = LeagueTeam::where('league_id', $league->id)
            ->with([
                'team',
                'auctioneer',
                'teamAuctioneer.auctioneer',
                'leaguePlayers' => function ($query) {
                    $query->with(['player.position', 'player.primaryGameRole.gamePosition'])
                        ->with(['player.localBody.district.state'])
                        ->where(function ($q) {
                            $q->whereIn('status', ['retained', 'sold'])
                                ->orWhere('retention', true);
                        })
                        ->orderByRaw("FIELD(status, 'retained', 'sold')")
                        ->orderBy('bid_price', 'desc');
                }
            ])
            ->withCount([
                'leaguePlayers as sold_players_count' => function ($query) {
                    $query->where('status', 'sold');
                }
            ])
            ->get();

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
        $teams = $teams->map(function ($team) use ($availableBasePrices, $league, $retainedCounts, $auctionSlotsPerTeam) {
            $soldCount = $team->sold_players_count ?? 0;
            $retainedCount = $retainedCounts[$team->id] ?? 0;
            $playersNeeded = max($auctionSlotsPerTeam - $soldCount, 0);
            $futureSlots = max($playersNeeded - 1, 0);
            $reserveAmount = $futureSlots > 0 ? $availableBasePrices->take($futureSlots)->sum() : 0;
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

        $viewerKey = "live_viewers:{$league->id}";
        $sessionId = session()->getId();
        $viewerSessions = Cache::get($viewerKey, []);
        $viewerSessions[$sessionId] = now()->timestamp;
        $threshold = now()->subMinutes(5)->timestamp;
        $viewerSessions = array_filter($viewerSessions, function ($timestamp) use ($threshold) {
            return $timestamp >= $threshold;
        });
        Cache::put($viewerKey, $viewerSessions, now()->addMinutes(10));
        $liveViewers = count($viewerSessions);

        $lastOutcomePlayer = LeaguePlayer::where('league_id', $league->id)
            ->whereIn('status', ['sold', 'unsold'])
            ->with([
                'player.position',
                'player.primaryGameRole.gamePosition',
                'player.localBody.district.state',
                'leagueTeam.team',
            ])
            ->latest('updated_at')
            ->first();

        $soldPlayersBaseQuery = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'sold')
            ->with([
                'user',
                'user.position',
                'league',
                'league.localBody.district',
                'leagueTeam.team.localBody',
            ]);

        $recentSoldPlayers = (clone $soldPlayersBaseQuery)
            ->latest('updated_at')
            ->take(30)
            ->get();

        $topSoldPlayers = (clone $soldPlayersBaseQuery)
            ->orderByDesc('bid_price')
            ->orderByDesc('updated_at')
            ->take(30)
            ->get();

        return compact(
            'league',
            'currentBids',
            'currentPlayer',
            'currentHighestBid',
            'teams',
            'liveViewers',
            'lastOutcomePlayer',
            'recentSoldPlayers',
            'topSoldPlayers'
        );
    }
}

