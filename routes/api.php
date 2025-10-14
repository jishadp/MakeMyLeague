<?php

use App\Http\Controllers\AuctionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('auction')->name('auction.')->group(function () {
    Route::post('start', [AuctionController::class, 'start'])->name('start');
    Route::post('pause', [AuctionController::class, 'pauseAuction'])->name('pause');
    Route::post('end', [AuctionController::class, 'endAuction'])->name('end');
    Route::post('settings', [AuctionController::class, 'updateAuctionSettings'])->name('settings');
    Route::get('stats', [AuctionController::class, 'getAuctionStats'])->name('stats');
});

// API routes for auction data
Route::get('auctions/league/{league:slug}/recent-bids', [AuctionController::class, 'getRecentBids'])
    ->name('api.auction.recent-bids')
    ->where('league', '[a-z0-9\-]+');
    
Route::get('auctions/league/{league:slug}/team-balances', [AuctionController::class, 'getTeamBalances'])
    ->name('api.auction.team-balances')
    ->where('league', '[a-z0-9\-]+');
