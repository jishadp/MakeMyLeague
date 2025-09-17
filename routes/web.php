<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroundController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\LeaguePlayerController;
use App\Http\Controllers\LeagueTeamController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Get 3 featured grounds (highest capacity) for the landing page
    $featuredGrounds = \App\Models\Ground::with(['state', 'district', 'localBody'])
        ->where('is_available', true)
        ->orderBy('capacity', 'desc')
        ->take(3)
        ->get();

    // Get 3 featured teams for the landing page
    $featuredTeams = \App\Models\Team::with(['homeGround', 'localBody'])
        ->take(3)
        ->get();


    return view('welcome', compact('featuredGrounds', 'featuredTeams'));
});

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('do-login', [LoginController::class, 'doLogin'])->name('do.login');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('do.register');

// Role selection routes
Route::middleware('auth')->group(function () {
    Route::get('role-selection', [App\Http\Controllers\RoleSelectionController::class, 'show'])->name('role-selection.show');
    Route::post('role-selection', [App\Http\Controllers\RoleSelectionController::class, 'store'])->name('role-selection.store');
});

// Ground routes
Route::get('grounds', [GroundController::class, 'index'])->name('grounds.index');
Route::get('grounds/{ground}', [GroundController::class, 'show'])->name('grounds.show');

// Team routes with slugs
Route::get('teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('teams/create', [TeamController::class, 'create'])->name('teams.create')->middleware('auth');
Route::post('teams', [TeamController::class, 'store'])->name('teams.store')->middleware('auth');
Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::get('teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit')->middleware('auth');
Route::put('teams/{team}', [TeamController::class, 'update'])->name('teams.update')->middleware('auth');
Route::delete('teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy')->middleware('auth');

// Player routes
Route::get('players', [PlayerController::class, 'index'])->name('players.index');
Route::get('players/create', [PlayerController::class, 'create'])->name('players.create')->middleware('auth');
Route::post('players', [PlayerController::class, 'store'])->name('players.store')->middleware('auth');
Route::get('players/{player}', [PlayerController::class, 'show'])->name('players.show');
Route::get('players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit')->middleware('auth');
Route::put('players/{player}', [PlayerController::class, 'update'])->name('players.update')->middleware('auth');
Route::delete('players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy')->middleware('auth');
Route::post('leagues/{league}/players/register', [PlayerController::class, 'register'])
    ->name('league-players.register');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'view'])->name('dashboard')->middleware('has.role');
    Route::get('dashboard/auctions', [DashboardController::class, 'auctionsIndex'])->name('auctions.index')->middleware('has.role');

    // Leagues resource routes
    Route::resource('leagues', LeagueController::class);
    Route::get('leagues/player/broadcast', [LeagueController::class, 'playerBroadcast'])->name('leagues.player.broadcast');
    Route::post('leagues/{league}/set-default', [LeagueController::class, 'setDefault'])->name('leagues.setDefault');
    Route::post('leagues/{league}/bid-increments', [LeagueController::class, 'updateBidIncrements'])->name('leagues.update-bid-increments');

    // League Teams routes
    Route::resource('leagues.league-teams', LeagueTeamController::class)->except(['show']);
    Route::get('leagues/{league}/teams', [LeagueTeamController::class, 'index'])->name('league-teams.index');
    Route::get('leagues/{league}/teams/create', [LeagueTeamController::class, 'create'])->name('league-teams.create');
    Route::post('leagues/{league}/teams', [LeagueTeamController::class, 'store'])->name('league-teams.store');
    Route::get('leagues/{league}/teams/{leagueTeam}', [LeagueTeamController::class, 'show'])->name('league-teams.show');
    Route::get('leagues/{league}/teams/{leagueTeam}/edit', [LeagueTeamController::class, 'edit'])->name('league-teams.edit');
    Route::put('leagues/{league}/teams/{leagueTeam}', [LeagueTeamController::class, 'update'])->name('league-teams.update');
    Route::delete('leagues/{league}/teams/{leagueTeam}', [LeagueTeamController::class, 'destroy'])->name('league-teams.destroy');
    Route::patch('leagues/{league}/teams/{leagueTeam}/status', [LeagueTeamController::class, 'updateStatus'])->name('league-teams.updateStatus');
    Route::patch('leagues/{league}/teams/{leagueTeam}/wallet', [LeagueTeamController::class, 'updateWallet'])->name('league-teams.updateWallet');

    // League Players routes
    Route::get('leagues/{league}/players', [LeaguePlayerController::class, 'index'])->name('league-players.index');
    Route::get('leagues/{league}/players/create', [LeaguePlayerController::class, 'create'])->name('league-players.create');
    Route::get('leagues/{league}/players/bulk-create', [LeaguePlayerController::class, 'bulkCreate'])->name('league-players.bulk-create');
    Route::post('leagues/{league}/players/bulk-store', [LeaguePlayerController::class, 'bulkStore'])->name('league-players.bulk-store');
    Route::post('leagues/{league}/players', [LeaguePlayerController::class, 'store'])->name('league-players.store');
    Route::get('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'show'])->name('league-players.show');
    Route::get('leagues/{league}/players/{leaguePlayer}/edit', [LeaguePlayerController::class, 'edit'])->name('league-players.edit');
    Route::put('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'update'])->name('league-players.update');
    Route::patch('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'update'])->name('league-players.patch');
    Route::delete('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'destroy'])->name('league-players.destroy');
    Route::patch('leagues/{league}/players/{leaguePlayer}/status', [LeaguePlayerController::class, 'updateStatus'])->name('league-players.updateStatus');
    Route::match(['post', 'patch'], 'leagues/{league}/players/bulk-status', [LeaguePlayerController::class, 'bulkUpdateStatus'])->name('league-players.bulkStatus');
    Route::post('leagues/{league}/players/request-registration', [LeaguePlayerController::class, 'requestRegistration'])->name('league-players.request-registration');

    // Auction routes
    Route::prefix('leagues/{league}/auction')->name('auction.')->group(function () {
        Route::get('/', [AuctionController::class, 'index'])->name('index');
        Route::post('place-bid', [AuctionController::class, 'placeBid'])->name('place-bid');
        Route::post('accept-bid', [AuctionController::class, 'acceptBid'])->name('accept-bid');
        Route::post('skip-player', [AuctionController::class, 'skipPlayer'])->name('skip-player');
        Route::post('current-bids', [AuctionController::class, 'getCurrentBids'])->name('current-bids');
    });

    Route::prefix('auction')->name('auction.')->group(function () {
        Route::post('call', [AuctionController::class, 'call'])->name('call');
        Route::post('sold', [AuctionController::class, 'sold'])->name('sold');
    });
});
