<?php

use App\Http\Controllers\AuctionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\LeagueApiController;

Route::post('login', [AuthApiController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('leagues', [LeagueApiController::class, 'index']);
});
Route::prefix('auction')->name('auction.')->group(function () {
    Route::post('start', [AuctionController::class, 'start'])->name('start');
    Route::post('pause', [AuctionController::class, 'pauseAuction'])->name('pause');
    Route::post('end', [AuctionController::class, 'endAuction'])->name('end');
    Route::post('settings', [AuctionController::class, 'updateAuctionSettings'])->name('settings');
    Route::get('stats', [AuctionController::class, 'getAuctionStats'])->name('stats');
});

// Auction API endpoints for live view
Route::prefix('auctions/league/{league:slug}')->group(function () {
    Route::get('team-balances', [AuctionController::class, 'getTeamBalances']);
    Route::get('recent-bids', [AuctionController::class, 'getRecentBids']);
});
