<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroundController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\LeaguePlayerController;
use App\Http\Controllers\LeagueTeamController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManualAuctionController;
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
        
    // Get 3 featured players for the landing page
    $featuredPlayers = \App\Models\User::with(['role', 'localBody'])
        ->players()
        ->take(3)
        ->get();
        
    return view('welcome', compact('featuredGrounds', 'featuredTeams', 'featuredPlayers'));
});

Route::get('login',[LoginController::class,'login'])->name('login');
Route::post('do-login',[LoginController::class,'doLogin'])->name('do.login');
Route::get('logout',[LoginController::class,'logout'])->name('logout');

// Public routes
Route::get('grounds', [GroundController::class, 'index'])->name('grounds.index');
Route::get('grounds/{ground}', [GroundController::class, 'show'])->name('grounds.show');

// Team routes
Route::get('teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');

// Player routes
Route::get('players', [PlayerController::class, 'index'])->name('players.index');
Route::get('players/{player}', [PlayerController::class, 'show'])->name('players.show');

Route::middleware('auth')->group(function(){
    Route::get('dashboard',[DashboardController::class,'view'])->name('dashboard');
    
    // Leagues resource routes
    Route::resource('leagues', LeagueController::class);
    Route::post('leagues/{league}/set-default', [LeagueController::class, 'setDefault'])->name('leagues.setDefault');
    
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
    Route::post('leagues/{league}/players', [LeaguePlayerController::class, 'store'])->name('league-players.store');
    Route::get('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'show'])->name('league-players.show');
    Route::get('leagues/{league}/players/{leaguePlayer}/edit', [LeaguePlayerController::class, 'edit'])->name('league-players.edit');
    Route::put('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'update'])->name('league-players.update');
    Route::delete('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'destroy'])->name('league-players.destroy');
    Route::patch('leagues/{league}/players/{leaguePlayer}/status', [LeaguePlayerController::class, 'updateStatus'])->name('league-players.updateStatus');
    Route::post('leagues/{league}/players/bulk-status', [LeaguePlayerController::class, 'bulkUpdateStatus'])->name('league-players.bulkStatus');
    
    // Auction routes
    Route::prefix('leagues/{league}/auction')->name('auction.')->group(function () {
        // Manual auction routes
        Route::get('manual', [ManualAuctionController::class, 'index'])->name('manual');
        Route::post('manual', [ManualAuctionController::class, 'store'])->name('manual.store');
        Route::post('manual/update-status', [ManualAuctionController::class, 'updatePlayerStatus'])->name('manual.update-status');
        Route::get('manual/search-players', [ManualAuctionController::class, 'searchPlayers'])->name('manual.search-players');
        Route::get('manual/team-wallet/{teamId}', [ManualAuctionController::class, 'getTeamWallet'])->name('manual.team-wallet');
        
        // Bidding auction routes
        Route::get('bidding', [AuctionController::class, 'index'])->name('bidding');
        Route::post('place-bid', [AuctionController::class, 'placeBid'])->name('place-bid');
        Route::post('accept-bid', [AuctionController::class, 'acceptBid'])->name('accept-bid');
        Route::get('current-bids/{leaguePlayer}', [AuctionController::class, 'getCurrentBids'])->name('current-bids');
    });
});


