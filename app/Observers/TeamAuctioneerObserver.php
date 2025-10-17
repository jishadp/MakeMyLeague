<?php

namespace App\Observers;

use App\Models\TeamAuctioneer;
use App\Services\AuctionAccessService;
use Illuminate\Support\Facades\Log;

class TeamAuctioneerObserver
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    /**
     * Handle the TeamAuctioneer "created" event.
     */
    public function created(TeamAuctioneer $teamAuctioneer): void
    {
        $this->handleAuctioneerAssignmentChange($teamAuctioneer, 'created');
    }

    /**
     * Handle the TeamAuctioneer "updated" event.
     */
    public function updated(TeamAuctioneer $teamAuctioneer): void
    {
        $this->handleAuctioneerAssignmentChange($teamAuctioneer, 'updated');
    }

    /**
     * Handle the TeamAuctioneer "deleted" event.
     */
    public function deleted(TeamAuctioneer $teamAuctioneer): void
    {
        $this->handleAuctioneerAssignmentChange($teamAuctioneer, 'deleted');
    }

    /**
     * Handle auctioneer assignment changes and update auction access control.
     */
    protected function handleAuctioneerAssignmentChange(TeamAuctioneer $teamAuctioneer, string $action): void
    {
        $league = $teamAuctioneer->league;
        $user = $teamAuctioneer->auctioneer;
        $leagueTeam = $teamAuctioneer->leagueTeam;

        // Refresh access cache for the affected user
        $this->auctionAccessService->refreshUserAccessCache($user, $league);
        
        Log::info("TeamAuctioneer {$teamAuctioneer->id} {$action} for league team {$leagueTeam->id} in league {$league->id}. Access control updated for user {$user->id}.");
    }
}
