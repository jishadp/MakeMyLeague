<?php

namespace App\Observers;

use App\Models\TeamOwner;
use App\Services\AuctionAccessService;
use Illuminate\Support\Facades\Log;

class TeamOwnerObserver
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    /**
     * Handle the TeamOwner "created" event.
     */
    public function created(TeamOwner $teamOwner): void
    {
        $this->handleTeamOwnershipChange($teamOwner, 'created');
    }

    /**
     * Handle the TeamOwner "updated" event.
     */
    public function updated(TeamOwner $teamOwner): void
    {
        $this->handleTeamOwnershipChange($teamOwner, 'updated');
    }

    /**
     * Handle the TeamOwner "deleted" event.
     */
    public function deleted(TeamOwner $teamOwner): void
    {
        $this->handleTeamOwnershipChange($teamOwner, 'deleted');
    }

    /**
     * Handle team ownership changes and update auction access control.
     */
    protected function handleTeamOwnershipChange(TeamOwner $teamOwner, string $action): void
    {
        $team = $teamOwner->team;
        $user = $teamOwner->user;

        // Get all leagues where this team participates
        $leagues = $team->leagues;

        foreach ($leagues as $league) {
            // Find the league team record
            $leagueTeam = $league->leagueTeams()->where('team_id', $team->id)->first();
            
            if ($leagueTeam) {
                // Refresh access cache for the affected user
                $this->auctionAccessService->refreshUserAccessCache($user, $league);
                
                Log::info("TeamOwner {$teamOwner->id} {$action} for team {$team->id} in league {$league->id}. Access control updated for user {$user->id}.");
            }
        }
    }
}
