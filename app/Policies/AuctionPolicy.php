<?php

namespace App\Policies;

use App\Models\User;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Services\AuctionAccessService;

class AuctionPolicy
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    public function selectPlayer(User $user, League $league): bool
    {
        return $this->auctionAccessService->isApprovedOrganizer($user->id, $league->id);
    }

    public function markSoldUnsold(User $user, League $league): bool
    {
        return $this->auctionAccessService->isApprovedOrganizer($user->id, $league->id);
    }

    public function placeBid(User $user, League $league): bool
    {
        $role = $this->auctionAccessService->getUserAuctionRole($user->id, $league->id);
        return in_array($role, ['auctioneer', 'both']);
    }

    public function startAuction(User $user, League $league): bool
    {
        if (!$this->auctionAccessService->isApprovedOrganizer($user->id, $league->id)) {
            return false;
        }

        $validation = $this->auctionAccessService->validateAuctionStart($league->id);
        return $validation['valid'];
    }

    public function viewAuctionPanel(User $user, League $league): bool
    {
        $role = $this->auctionAccessService->getUserAuctionRole($user->id, $league->id);
        return $role !== 'none';
    }
}
