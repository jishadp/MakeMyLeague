<?php

use Illuminate\Support\Facades\Broadcast;
use App\Services\AuctionAccessService;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('auction.{leagueId}', function ($user, $leagueId) {
    $auctionAccessService = app(AuctionAccessService::class);
    $accessCheck = $auctionAccessService->canUserAccessAuction($user->id, $leagueId);
    
    if ($accessCheck['allowed']) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $accessCheck['role'],
            'team_id' => $accessCheck['team_id']
        ];
    }
    
    return false;
});

Broadcast::channel('league.{leagueId}', function ($user, $leagueId) {
    return true; // Public channel for league updates
});
