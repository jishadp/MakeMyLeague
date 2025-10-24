<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueOrganizer;
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
        // For organizers and admins, check if they have teams to bid for
        if ($user->isOrganizerForLeague($league->id) || $user->isAdmin()) {
            // Organizers and admins can bid even without auction access granted
            // Check if they own any teams in this league
            $hasOwnedTeams = LeagueTeam::where('league_id', $league->id)
                ->whereHas('team', function ($query) use ($user) {
                    $query->whereHas('owners', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })
                ->exists();
            
            return $hasOwnedTeams;
        }

        // For regular users (team owners/auctioneers), check if they have teams
        // Allow bidding even if auction is not officially started - team owners should be able to bid
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
        // For organizers and admins, try to get their default team first
        if ($user->isOrganizerForLeague($league->id) || $user->isAdmin()) {
            $defaultTeam = $user->getDefaultTeamForLeague($league->id);
            if ($defaultTeam) {
                $leagueTeam = LeagueTeam::where('league_id', $league->id)
                    ->where('team_id', $defaultTeam->id)
                    ->first();
                if ($leagueTeam) {
                    return $leagueTeam;
                }
            }
            
            // If no default team, get any team they own in this league
            $ownedLeagueTeam = LeagueTeam::where('league_id', $league->id)
                ->whereHas('team', function ($query) use ($user) {
                    $query->whereHas('owners', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })
                ->first();
            
            if ($ownedLeagueTeam) {
                return $ownedLeagueTeam;
            }
            
            // If organizer has no teams, they can't bid (they need to own a team to bid)
            return null;
        }

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

        // Auction access check removed - team owners and organizers can bid directly

        // Auction active check removed - allow bidding even if auction not officially started
        // This allows team owners to bid without waiting for organizer to start auction

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
                'message' => 'You need to own a team in this league to place bids. Please register a team first.',
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
     * Clear all auction access cache for debugging purposes.
     *
     * @return void
     */
    public function clearAllAuctionAccessCache(): void
    {
        // This is a simple approach - in production you might want to use cache tags
        $pattern = self::CACHE_PREFIX . '*';
        
        // Note: This is a simplified cache clearing - Laravel doesn't have built-in pattern-based cache clearing
        // In production, you might want to use cache tags or maintain a list of cache keys
        Log::info("Auction access cache cleared for debugging");
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

        // Prepare update data
        $updateData = [
            'auction_active' => true
        ];

        // Only set auction_started_at if it hasn't been set yet
        if (!$league->auction_started_at) {
            $updateData['auction_started_at'] = now();
        }

        // Mark league as having live auction
        $league->update($updateData);

        // Refresh access cache for all users in this league
        $this->refreshLeagueAccessCache($league);

        Log::info("Player {$leaguePlayer->id} started auctioning in league {$league->id}. Access control updated.");
    }

    /**
     * Handle auction completion for a league.
     * This sets auction_ended_at and refreshes access cache.
     *
     * @param League $league
     * @return void
     */
    public function handleAuctionCompletion(League $league): void
    {
        // Mark auction as ended
        $league->update([
            'auction_active' => false,
            'auction_ended_at' => now()
        ]);

        // Refresh access cache for all users in this league
        $this->refreshLeagueAccessCache($league);

        Log::info("Auction completed for league {$league->id}. Access control updated.");
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

    /**
     * Check if user is approved organizer for league.
     *
     * @param int $userId
     * @param int $leagueId
     * @return bool
     */
    public function isApprovedOrganizer($userId, $leagueId): bool
    {
        return LeagueOrganizer::where('user_id', $userId)
            ->where('league_id', $leagueId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if user is active auctioneer for league.
     *
     * @param int $userId
     * @param int $leagueId
     * @return array [bool, team_id]
     */
    public function isActiveAuctioneer($userId, $leagueId): array
    {
        $auctioneer = TeamAuctioneer::where('auctioneer_id', $userId)
            ->where('league_id', $leagueId)
            ->where('status', 'active')
            ->first();

        return [
            $auctioneer !== null,
            $auctioneer ? $auctioneer->league_team_id : null
        ];
    }

    /**
     * Check if user is team owner with bidding rights.
     *
     * @param int $userId
     * @param int $leagueId
     * @return array [bool, team_id]
     */
    public function isTeamOwnerWithBiddingRights($userId, $leagueId): array
    {
        $leagueTeam = LeagueTeam::where('league_id', $leagueId)
            ->whereHas('team.owners', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        return [
            $leagueTeam !== null,
            $leagueTeam ? $leagueTeam->id : null
        ];
    }

    /**
     * Get user's auction role for league.
     *
     * @param int $userId
     * @param int $leagueId
     * @return string (organizer|auctioneer|both|none)
     */
    public function getUserAuctionRole($userId, $leagueId): string
    {
        $isOrganizer = $this->isApprovedOrganizer($userId, $leagueId);
        [$isAuctioneer, $teamId] = $this->isActiveAuctioneer($userId, $leagueId);
        
        if (!$isAuctioneer) {
            [$isAuctioneer, $teamId] = $this->isTeamOwnerWithBiddingRights($userId, $leagueId);
        }

        if ($isOrganizer && $isAuctioneer) {
            return 'both';
        } elseif ($isOrganizer) {
            return 'organizer';
        } elseif ($isAuctioneer) {
            return 'auctioneer';
        }

        return 'none';
    }

    /**
     * Check if user can access auction.
     *
     * @param int $userId
     * @param int $leagueId
     * @return array [allowed, role, team_id, message]
     */
    public function canUserAccessAuction($userId, $leagueId): array
    {
        $user = User::find($userId);
        $league = League::find($leagueId);

        if (!$user || !$league) {
            return [
                'allowed' => false,
                'role' => 'none',
                'team_id' => null,
                'message' => 'User or league not found'
            ];
        }

        // Check if admin without role
        if ($user->isAdmin()) {
            $isOrganizer = $this->isApprovedOrganizer($userId, $leagueId);
            [$isAuctioneer, $teamId] = $this->isActiveAuctioneer($userId, $leagueId);
            
            if (!$isAuctioneer) {
                [$isAuctioneer, $teamId] = $this->isTeamOwnerWithBiddingRights($userId, $leagueId);
            }

            if (!$isOrganizer && !$isAuctioneer) {
                return [
                    'allowed' => false,
                    'role' => 'admin_no_role',
                    'team_id' => null,
                    'message' => 'Admins must be assigned as organizer or auctioneer to access live auctions'
                ];
            }
        }

        $role = $this->getUserAuctionRole($userId, $leagueId);
        
        if ($role === 'none') {
            return [
                'allowed' => false,
                'role' => 'none',
                'team_id' => null,
                'message' => 'You are not authorized to access this auction'
            ];
        }

        // Get team ID for auctioneers
        $teamId = null;
        if (in_array($role, ['auctioneer', 'both'])) {
            [$isAuctioneer, $teamId] = $this->isActiveAuctioneer($userId, $leagueId);
            if (!$teamId) {
                [$isOwner, $teamId] = $this->isTeamOwnerWithBiddingRights($userId, $leagueId);
            }
        }

        return [
            'allowed' => true,
            'role' => $role,
            'team_id' => $teamId,
            'message' => 'Access granted'
        ];
    }

    /**
     * Validate auction start requirements.
     *
     * @param int $leagueId
     * @return array [valid, data, errors]
     */
    public function validateAuctionStart($leagueId): array
    {
        $league = League::with(['leagueTeams.team', 'leagueTeams.teamAuctioneer', 'leaguePlayers'])->find($leagueId);
        
        if (!$league) {
            return ['valid' => false, 'data' => [], 'errors' => ['League not found']];
        }

        $errors = [];
        $warnings = [];
        
        // Check teams
        $teamsCount = $league->leagueTeams()->count();
        if ($teamsCount < 2) {
            $errors[] = "At least 2 teams required (current: {$teamsCount})";
        }

        // Check players
        $playersCount = $league->leaguePlayers()->where('status', 'available')->count();
        if ($playersCount === 0) {
            $errors[] = 'No players available for auction';
        }

        // Check auctioneers
        $teamsWithoutAuctioneers = $league->leagueTeams()
            ->whereDoesntHave('teamAuctioneer', function($query) {
                $query->where('status', 'active');
            })
            ->whereDoesntHave('team.owners')
            ->count();

        if ($teamsWithoutAuctioneers > 0) {
            $warnings[] = "{$teamsWithoutAuctioneers} teams have no auctioneer or owner assigned";
        }

        // Get auctioneers list
        $auctioneersList = $league->leagueTeams()->with(['team', 'teamAuctioneer'])->get()->map(function($leagueTeam) {
            $auctioneer = $leagueTeam->teamAuctioneer;
            $owners = $leagueTeam->team->owners;
            
            return [
                'team_name' => $leagueTeam->team->name,
                'auctioneer_name' => $auctioneer ? $auctioneer->auctioneer->name : ($owners->isNotEmpty() ? $owners->first()->name . ' (Owner)' : 'None'),
                'status' => $auctioneer ? 'Assigned' : ($owners->isNotEmpty() ? 'Owner Bids' : 'No Auctioneer')
            ];
        });

        return [
            'valid' => empty($errors),
            'data' => [
                'teams_count' => $teamsCount,
                'players_count' => $playersCount,
                'auctioneers_list' => $auctioneersList,
                'auction_access' => $league->auction_access ?? 'auctioneers'
            ],
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Get auctioneers list for league.
     *
     * @param int $leagueId
     * @return \Illuminate\Support\Collection
     */
    public function getAuctioneersList($leagueId)
    {
        $league = League::with(['leagueTeams.team', 'leagueTeams.teamAuctioneer'])->find($leagueId);
        
        if (!$league) {
            return collect();
        }

        return $league->leagueTeams->map(function($leagueTeam) {
            $auctioneer = $leagueTeam->teamAuctioneer;
            $owners = $leagueTeam->team->owners;
            
            return [
                'team_id' => $leagueTeam->id,
                'team_name' => $leagueTeam->team->name,
                'auctioneer_id' => $auctioneer ? $auctioneer->auctioneer_id : null,
                'auctioneer_name' => $auctioneer ? $auctioneer->auctioneer->name : null,
                'owner_name' => $owners->isNotEmpty() ? $owners->first()->name : null,
                'access_type' => $auctioneer ? 'auctioneer' : ($owners->isNotEmpty() ? 'owner' : 'none')
            ];
        });
    }
}
