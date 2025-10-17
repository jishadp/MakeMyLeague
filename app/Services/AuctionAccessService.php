<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\TeamAuctioneer;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuctionAccessService
{
    /**
     * Cache key prefix for auction access
     */
    const CACHE_PREFIX = 'auction_access_';

    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 60;

    /**
     * Check if a user can bid in a specific league auction.
     *
     * @param User $user
     * @param League $league
     * @return bool
     */
    public function canUserBidInLeague(User $user, League $league): bool
    {
        // Check if league has auction access granted
        if (!$league->hasAuctionAccess()) {
            return false;
        }

        // Check if auction is active
        if (!$league->isAuctionActive()) {
            return false;
        }

        // Check if user has any team in this league
        $userTeams = $this->getUserTeamsInLeague($user, $league);
        
        return $userTeams->isNotEmpty();
    }

    /**
     * Get all teams a user can bid for in a specific league.
     *
     * @param User $user
     * @param League $league
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTeamsInLeague(User $user, League $league)
    {
        $cacheKey = self::CACHE_PREFIX . "user_{$user->id}_league_{$league->id}_teams";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user, $league) {
            $teams = collect();

            // 1. Check if user is assigned as auctioneer for any team in this league
            $auctioneerTeams = LeagueTeam::where('league_id', $league->id)
                ->whereHas('teamAuctioneer', function ($query) use ($user) {
                    $query->where('auctioneer_id', $user->id)
                          ->where('status', 'active');
                })
                ->with(['team', 'teamAuctioneer'])
                ->get();

            foreach ($auctioneerTeams as $leagueTeam) {
                $teams->push([
                    'league_team' => $leagueTeam,
                    'team' => $leagueTeam->team,
                    'access_type' => 'auctioneer',
                    'access_source' => 'assigned_auctioneer'
                ]);
            }

            // 2. Check if user owns any teams in this league
            $ownedTeams = LeagueTeam::where('league_id', $league->id)
                ->whereHas('team', function ($query) use ($user) {
                    $query->whereHas('owners', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })
                ->with(['team.owners' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->get();

            foreach ($ownedTeams as $leagueTeam) {
                // Skip if user is already included as auctioneer for this team
                if (!$teams->contains(function ($item) use ($leagueTeam) {
                    return $item['league_team']->id === $leagueTeam->id;
                })) {
                    $teams->push([
                        'league_team' => $leagueTeam,
                        'team' => $leagueTeam->team,
                        'access_type' => 'owner',
                        'access_source' => 'team_owner'
                    ]);
                }
            }

            return $teams;
        });
    }

    /**
     * Get the specific team a user should bid for in a league.
     * Handles multiple teams scenario using default team logic.
     *
     * @param User $user
     * @param League $league
     * @return LeagueTeam|null
     */
    public function getUserBiddingTeam(User $user, League $league): ?LeagueTeam
    {
        $userTeams = $this->getUserTeamsInLeague($user, $league);

        if ($userTeams->isEmpty()) {
            return null;
        }

        // If user has only one team, return it
        if ($userTeams->count() === 1) {
            return $userTeams->first()['league_team'];
        }

        // If user has multiple teams, use default team logic
        $defaultTeam = $user->getDefaultTeamForLeague($league->id);
        
        if ($defaultTeam) {
            $defaultLeagueTeam = $userTeams->firstWhere(function ($item) use ($defaultTeam) {
                return $item['team']->id === $defaultTeam->id;
            });
            
            if ($defaultLeagueTeam) {
                return $defaultLeagueTeam['league_team'];
            }
        }

        // Fallback: return the first team (prioritize auctioneer assignments)
        $auctioneerTeam = $userTeams->firstWhere('access_type', 'auctioneer');
        if ($auctioneerTeam) {
            return $auctioneerTeam['league_team'];
        }

        return $userTeams->first()['league_team'];
    }

    /**
     * Validate if a user can place a bid for a specific player.
     *
     * @param User $user
     * @param LeaguePlayer $leaguePlayer
     * @return array ['valid' => bool, 'message' => string, 'league_team' => LeagueTeam|null]
     */
    public function validateBidAccess(User $user, LeaguePlayer $leaguePlayer): array
    {
        $league = $leaguePlayer->league;

        // Check if player is currently being auctioned
        if ($leaguePlayer->status !== 'auctioning') {
            return [
                'valid' => false,
                'message' => 'This player is not currently being auctioned.',
                'league_team' => null
            ];
        }

        // Check if user can bid in this league
        if (!$this->canUserBidInLeague($user, $league)) {
            return [
                'valid' => false,
                'message' => 'You are not authorized to bid in this league auction.',
                'league_team' => null
            ];
        }

        // Get the team user should bid for
        $biddingTeam = $this->getUserBiddingTeam($user, $league);
        
        if (!$biddingTeam) {
            return [
                'valid' => false,
                'message' => 'You are not authorized to bid for any team in this league.',
                'league_team' => null
            ];
        }

        return [
            'valid' => true,
            'message' => 'Bid access validated successfully.',
            'league_team' => $biddingTeam
        ];
    }

    /**
     * Refresh auction access cache for a user in a specific league.
     * Call this when team ownership or auctioneer assignments change.
     *
     * @param User $user
     * @param League $league
     * @return void
     */
    public function refreshUserAccessCache(User $user, League $league): void
    {
        $cacheKey = self::CACHE_PREFIX . "user_{$user->id}_league_{$league->id}_teams";
        Cache::forget($cacheKey);
        
        Log::info("Auction access cache refreshed for user {$user->id} in league {$league->id}");
    }

    /**
     * Refresh auction access cache for all users in a specific league.
     * Call this when league auction status changes.
     *
     * @param League $league
     * @return void
     */
    public function refreshLeagueAccessCache(League $league): void
    {
        // Get all users who might have access to this league
        $userIds = collect();

        // Get team owners
        $ownerIds = $league->leagueTeams()
            ->whereHas('team.owners')
            ->with('team.owners')
            ->get()
            ->pluck('team.owners')
            ->flatten()
            ->pluck('user_id');

        $userIds = $userIds->merge($ownerIds);

        // Get auctioneers
        $auctioneerIds = $league->leagueTeams()
            ->whereHas('teamAuctioneer')
            ->with('teamAuctioneer')
            ->get()
            ->pluck('teamAuctioneer')
            ->flatten()
            ->pluck('auctioneer_id');

        $userIds = $userIds->merge($auctioneerIds);

        // Clear cache for all relevant users
        foreach ($userIds->unique() as $userId) {
            $cacheKey = self::CACHE_PREFIX . "user_{$userId}_league_{$league->id}_teams";
            Cache::forget($cacheKey);
        }

        Log::info("Auction access cache refreshed for all users in league {$league->id}");
    }

    /**
     * Handle player status change to 'auctioning'.
     * This triggers league live auction mode and access control setup.
     *
     * @param LeaguePlayer $leaguePlayer
     * @return void
     */
    public function handlePlayerAuctioning(LeaguePlayer $leaguePlayer): void
    {
        $league = $leaguePlayer->league;

        // Mark league as having live auction
        $league->update([
            'auction_active' => true,
            'auction_started_at' => now()
        ]);

        // Refresh access cache for all users in this league
        $this->refreshLeagueAccessCache($league);

        Log::info("Player {$leaguePlayer->id} started auctioning in league {$league->id}. Access control updated.");
    }

    /**
     * Handle team ownership change.
     * This revokes access from previous owner and grants to new owner.
     *
     * @param LeagueTeam $leagueTeam
     * @param User|null $previousOwner
     * @param User|null $newOwner
     * @return void
     */
    public function handleTeamOwnershipChange(LeagueTeam $leagueTeam, ?User $previousOwner = null, ?User $newOwner = null): void
    {
        $league = $leagueTeam->league;

        // Refresh cache for previous owner if provided
        if ($previousOwner) {
            $this->refreshUserAccessCache($previousOwner, $league);
        }

        // Refresh cache for new owner if provided
        if ($newOwner) {
            $this->refreshUserAccessCache($newOwner, $league);
        }

        // If no specific owners provided, refresh for all users in the league
        if (!$previousOwner && !$newOwner) {
            $this->refreshLeagueAccessCache($league);
        }

        Log::info("Team ownership changed for league team {$leagueTeam->id} in league {$league->id}. Access control updated.");
    }

    /**
     * Handle auctioneer assignment change.
     * This updates access control for the affected users.
     *
     * @param LeagueTeam $leagueTeam
     * @param User|null $previousAuctioneer
     * @param User|null $newAuctioneer
     * @return void
     */
    public function handleAuctioneerAssignmentChange(LeagueTeam $leagueTeam, ?User $previousAuctioneer = null, ?User $newAuctioneer = null): void
    {
        $league = $leagueTeam->league;

        // Refresh cache for previous auctioneer if provided
        if ($previousAuctioneer) {
            $this->refreshUserAccessCache($previousAuctioneer, $league);
        }

        // Refresh cache for new auctioneer if provided
        if ($newAuctioneer) {
            $this->refreshUserAccessCache($newAuctioneer, $league);
        }

        // If no specific auctioneers provided, refresh for all users in the league
        if (!$previousAuctioneer && !$newAuctioneer) {
            $this->refreshLeagueAccessCache($league);
        }

        Log::info("Auctioneer assignment changed for league team {$leagueTeam->id} in league {$league->id}. Access control updated.");
    }

    /**
     * Get auction access statistics for a league.
     *
     * @param League $league
     * @return array
     */
    public function getAuctionAccessStats(League $league): array
    {
        $totalTeams = $league->leagueTeams()->count();
        $teamsWithOwners = $league->leagueTeams()
            ->whereHas('team.owners')
            ->count();
        $teamsWithAuctioneers = $league->leagueTeams()
            ->whereHas('teamAuctioneer', function ($query) {
                $query->where('status', 'active');
            })
            ->count();

        // Get unique users who can bid
        $biddingUsers = collect();
        
        // Get team owners
        $owners = $league->leagueTeams()
            ->whereHas('team.owners')
            ->with('team.owners')
            ->get()
            ->pluck('team.owners')
            ->flatten()
            ->pluck('user_id')
            ->unique();

        // Get auctioneers
        $auctioneers = $league->leagueTeams()
            ->whereHas('teamAuctioneer', function ($query) {
                $query->where('status', 'active');
            })
            ->with('teamAuctioneer')
            ->get()
            ->pluck('teamAuctioneer')
            ->flatten()
            ->pluck('auctioneer_id')
            ->unique();

        $biddingUsers = $biddingUsers->merge($owners)->merge($auctioneers)->unique();

        return [
            'total_teams' => $totalTeams,
            'teams_with_owners' => $teamsWithOwners,
            'teams_with_auctioneers' => $teamsWithAuctioneers,
            'total_bidding_users' => $biddingUsers->count(),
            'auction_active' => $league->auction_active,
            'auction_access_granted' => $league->auction_access_granted,
        ];
    }
}
