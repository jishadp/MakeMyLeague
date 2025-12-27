<?php

use App\Http\Controllers\AuctionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\LeagueApiController;

Route::post('login', [AuthApiController::class, 'login']);
Route::post('register', [AuthApiController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('leagues', [LeagueApiController::class, 'index']);
});

Route::prefix('auction')->name('auction.')->group(function () {
    Route::post('start', [AuctionController::class, 'start'])->name('start');
    Route::post('pause', [AuctionController::class, 'pause'])->name('pause');
    Route::post('end', [AuctionController::class, 'end'])->name('end');
    Route::post('settings', [AuctionController::class, 'updateAuctionSettings'])->name('settings');
    Route::get('stats', [AuctionController::class, 'getAuctionStats'])->name('stats');
});

Route::middleware('auth:sanctum')->prefix('auction')->name('api.auction.')->group(function () {
    Route::post('call', [AuctionController::class, 'call'])->name('call');
    Route::post('start-random', [AuctionController::class, 'startRandomPlayer'])->name('start-random');
    Route::post('sold', [AuctionController::class, 'sold'])->name('sold');
    Route::post('unsold', [AuctionController::class, 'unsold'])->name('unsold');
    Route::post('reset-bids', [AuctionController::class, 'resetBids'])->name('reset-bids');
    Route::get('search-players', [AuctionController::class, 'searchPlayers'])->name('search-players');
    Route::post('league/{league}/skip', [AuctionController::class, 'skipPlayer'])->name('skip');
    Route::get('league/{league}/available-players', [AuctionController::class, 'getAvailablePlayers'])->name('available-players');
    Route::post('update-rules/{league}', [AuctionController::class, 'updateBidRules'])->name('update-rules');
    Route::get('league/{league:slug}/control-room', [AuctionController::class, 'getControlRoomData'])->name('control-room');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/leagues', [LeagueApiController::class, 'index']);
    Route::get('/leagues/{slug}', [LeagueApiController::class, 'show']);
    Route::get('/dashboard/widgets', [DashboardApiController::class, 'index']);
    Route::get('leagues/{league:slug}/teams', [\App\Http\Controllers\Api\LeagueTeamApiController::class, 'index']);
    Route::get('leagues/{league:slug}/players', [\App\Http\Controllers\Api\LeaguePlayerApiController::class, 'index']);
    
    // User specific routes
    Route::get('my-leagues', [\App\Http\Controllers\Api\MyLeaguesController::class, 'index']);
    Route::get('my-teams', [\App\Http\Controllers\Api\MyTeamsController::class, 'index']);
    Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'update']);
});

// Auction API endpoints for live view  
Route::middleware('auth:sanctum')->get('live-auctions', [AuctionController::class, 'getLiveAuctions']);

// Live Matches API (Mobile)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('live-matches-listing', [\App\Http\Controllers\Api\LiveMatchApiController::class, 'index']);
    Route::get('leagues/{league:slug}/matches', [\App\Http\Controllers\Api\LiveMatchApiController::class, 'leagueMatches']);
    Route::get('fixtures/{fixture:slug}/details', [\App\Http\Controllers\Api\LiveMatchApiController::class, 'show']);
});

Route::prefix('auctions/league/{league:slug}')->group(function () {
    Route::get('current-state', [AuctionController::class, 'getCurrentState']);
    Route::get('team-balances', [AuctionController::class, 'getTeamBalances']);
    Route::get('recent-bids', [AuctionController::class, 'getRecentBids']);
});

Route::prefix('leagues/{league:slug}')->group(function () {
    Route::get('sold-players', [AuctionController::class, 'getSoldPlayers']);
    Route::get('sold-players/pdf', [AuctionController::class, 'downloadSoldPlayersPDF']);
    Route::get('sold-players/csv', [AuctionController::class, 'downloadSoldPlayersCSV']);
});
