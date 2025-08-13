<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('leagues.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// League Manager routes
Route::resource('leagues', LeagueController::class)->middleware('auth');
Route::post('leagues/{league}/teams', [LeagueController::class, 'addTeams'])->name('leagues.add-teams')->middleware('auth');
Route::post('leagues/{league}/players', [LeagueController::class, 'addPlayers'])->name('leagues.add-players')->middleware('auth');

// League Team Management routes
Route::get('leagues/{league}/teams/create', [App\Http\Controllers\LeagueTeamController::class, 'create'])->name('league-teams.create')->middleware('auth');
Route::post('leagues/{league}/teams', [App\Http\Controllers\LeagueTeamController::class, 'store'])->name('league-teams.store')->middleware('auth');
Route::delete('leagues/{league}/teams/{team}', [App\Http\Controllers\LeagueTeamController::class, 'destroy'])->name('league-teams.destroy')->middleware('auth');

// Auction routes
Route::get('leagues/{league}/auction', [App\Http\Controllers\AuctionController::class, 'show'])->name('auction.show')->middleware('auth');
Route::get('leagues/{league}/auction/setup', [App\Http\Controllers\AuctionController::class, 'setup'])->name('auction.setup')->middleware('auth');
Route::post('leagues/{league}/auction/start', [App\Http\Controllers\AuctionController::class, 'start'])->name('auction.start')->middleware('auth');
Route::post('leagues/{league}/auction/bid', [App\Http\Controllers\AuctionController::class, 'bid'])->name('auction.bid')->middleware('auth');
Route::post('leagues/{league}/auction/reset', [App\Http\Controllers\AuctionController::class, 'reset'])->name('auction.reset')->middleware('auth');
Route::get('leagues/{league}/auction/public', [App\Http\Controllers\AuctionController::class, 'public'])->name('auction.public');

// Global entities routes (public access)
Route::resource('players', PlayerController::class);
Route::resource('teams', TeamController::class);

// Public Auction routes
Route::get('/auctions', [App\Http\Controllers\PublicAuctionController::class, 'index'])->name('auctions.index');

require __DIR__.'/auth.php';
