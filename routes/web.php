<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuctioneerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroundController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\LeaguePlayerController;
use App\Http\Controllers\LeagueTeamController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LeagueFinanceController;
use App\Http\Controllers\OrganizerRequestController;
use App\Http\Controllers\TeamTransferController;
use App\Http\Controllers\DefaultTeamController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\OrganizerRequestController as AdminOrganizerRequestController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\GroundController as AdminGroundController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminUserController as AdminAdminUserController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('do-login', [LoginController::class, 'doLogin'])->name('do.login');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('do.register');
Route::get('api/local-bodies', [RegisterController::class, 'getLocalBodiesByDistrict'])->name('api.local-bodies');

// Ground routes
Route::get('grounds', [GroundController::class, 'index'])->name('grounds.index');
Route::get('grounds/{ground}', [GroundController::class, 'show'])->name('grounds.show');

// Team routes with slugs
Route::get('teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('teams/league-teams', [TeamController::class, 'leagueTeams'])->name('teams.league-teams');
Route::get('teams/league-players', [TeamController::class, 'leaguePlayers'])->name('teams.league-players');
Route::get('teams/create', [TeamController::class, 'create'])->name('teams.create')->middleware('auth');
Route::post('teams', [TeamController::class, 'store'])->name('teams.store')->middleware('auth');
Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::get('teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit')->middleware('auth');
Route::put('teams/{team}', [TeamController::class, 'update'])->name('teams.update')->middleware('auth');
Route::delete('teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy')->middleware('auth');
Route::post('teams/{team}/upload-logo', [TeamController::class, 'uploadLogo'])->name('teams.upload-logo')->middleware('auth');
Route::post('teams/{team}/upload-banner', [TeamController::class, 'uploadBanner'])->name('teams.upload-banner')->middleware('auth');
Route::delete('teams/{team}/remove-logo', [TeamController::class, 'removeLogo'])->name('teams.remove-logo')->middleware('auth');
Route::delete('teams/{team}/remove-banner', [TeamController::class, 'removeBanner'])->name('teams.remove-banner')->middleware('auth');

// Player routes
Route::get('players', [PlayerController::class, 'index'])->name('players.index');
Route::get('players/create', [PlayerController::class, 'create'])->name('players.create')->middleware('auth');
Route::post('players', [PlayerController::class, 'store'])->name('players.store')->middleware('auth');
Route::get('players/{player}', [PlayerController::class, 'show'])->name('players.show');
Route::get('players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit')->middleware('auth');
Route::put('players/{player}', [PlayerController::class, 'update'])->name('players.update')->middleware('auth');
Route::delete('players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy')->middleware('auth');
// Simple player registration route
Route::post('register-player/{leagueId}', [LeaguePlayerController::class, 'simpleRegister'])
    ->name('register-player')->middleware('auth');

Route::post('leagues/{league}/players/register', [PlayerController::class, 'register'])
    ->name('league-players.register');

// League join link routes
Route::get('join-league/{league}', [LeagueController::class, 'showJoinLink'])->name('leagues.join-link');
Route::post('join-league/{league}', [LeagueController::class, 'processJoinLink'])->name('leagues.process-join');

// League shareable public page
Route::get('leagues/{league}/share', [LeagueController::class, 'shareable'])->name('leagues.shareable');

// Public live auction route (no auth required)
Route::get('dashboard/auctions/{league}/live', [DashboardController::class, 'liveAuction'])->name('auctions.live');
Route::get('dashboard/auctions/{league}/broadcast', [DashboardController::class, 'liveAuctionPublic'])->name('auctions.live.public');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'view'])->name('dashboard');
    Route::get('dashboard/auctions', [DashboardController::class, 'auctionsIndex'])->name('auctions.index');
    
    // Profile routes
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // My Leagues route
    Route::get('my-leagues', [\App\Http\Controllers\MyLeaguesController::class, 'index'])->name('my-leagues');
    Route::get('my-leagues/{league}/teams', [\App\Http\Controllers\MyLeaguesController::class, 'showTeams'])->name('my-leagues.teams');
    
    // My Teams route
    Route::get('my-teams', [\App\Http\Controllers\MyTeamsController::class, 'index'])->name('my-teams');
    
    // My Auctions route
    Route::get('auctions', [\App\Http\Controllers\AuctionsController::class, 'index'])->name('auctions.index');

    // Auctioneer assignment routes - only team owners can assign auctioneers
    Route::prefix('leagues/{league}/teams/{leagueTeam}')->middleware('team.owner')->group(function () {
        Route::post('auctioneer/assign', [AuctioneerController::class, 'assign'])->name('auctioneer.assign');
        Route::delete('auctioneer/remove', [AuctioneerController::class, 'remove'])->name('auctioneer.remove');
    });
    
    // Auctioneer search and availability routes
    Route::prefix('leagues/{league}')->group(function () {
        Route::get('auctioneers/available', [AuctioneerController::class, 'getAvailableAuctioneers'])->name('auctioneers.available');
        Route::get('auctioneers/search', [AuctioneerController::class, 'searchUsers'])->name('auctioneers.search');
    });

    // Notification routes
    Route::post('notifications/{notification}/mark-read', function (\App\Models\Notification $notification) {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');

    // Team transfer routes
    Route::prefix('teams/{team}')->group(function () {
        Route::post('transfer', [TeamTransferController::class, 'transfer'])->name('teams.transfer');
    });
    
    // Team transfer search route
    Route::get('team-transfer/search', [TeamTransferController::class, 'searchUsers'])->name('team-transfer.search');

    // Default team management routes
    Route::post('default-team/set', [DefaultTeamController::class, 'setDefault'])->name('default-team.set');
    Route::delete('default-team/remove', [DefaultTeamController::class, 'removeDefault'])->name('default-team.remove');
    Route::get('default-team/owned-teams', [DefaultTeamController::class, 'getOwnedTeams'])->name('default-team.owned-teams');



    // Organizer Request routes
    Route::resource('organizer-requests', OrganizerRequestController::class)->except(['edit', 'update']);
    Route::delete('organizer-requests/{organizerRequest}/cancel', [OrganizerRequestController::class, 'cancel'])->name('organizer-requests.cancel');

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('organizer-requests', [AdminOrganizerRequestController::class, 'index'])->name('organizer-requests.index');
        Route::get('organizer-requests/pending', [AdminOrganizerRequestController::class, 'pending'])->name('organizer-requests.pending');
        Route::get('organizer-requests/{organizerRequest}', [AdminOrganizerRequestController::class, 'show'])->name('organizer-requests.show');
        Route::post('organizer-requests/{organizerRequest}/approve', [AdminOrganizerRequestController::class, 'approve'])->name('organizer-requests.approve');
        Route::post('organizer-requests/{organizerRequest}/reject', [AdminOrganizerRequestController::class, 'reject'])->name('organizer-requests.reject');
        Route::post('organizer-requests/{organizerRequest}/change-league-status', [AdminOrganizerRequestController::class, 'changeLeagueStatus'])->name('organizer-requests.change-league-status');
        Route::get('organizer-requests-stats', [AdminOrganizerRequestController::class, 'stats'])->name('organizer-requests.stats');
        
        // Location Management Routes
        Route::get('locations', [AdminLocationController::class, 'index'])->name('locations.index');
        Route::get('locations/create-state', [AdminLocationController::class, 'createState'])->name('locations.create-state');
        Route::post('locations/create-state', [AdminLocationController::class, 'storeState'])->name('locations.store-state');
        Route::get('locations/create-district', [AdminLocationController::class, 'createDistrict'])->name('locations.create-district');
        Route::post('locations/create-district', [AdminLocationController::class, 'storeDistrict'])->name('locations.store-district');
        Route::get('locations/create-local-body', [AdminLocationController::class, 'createLocalBody'])->name('locations.create-local-body');
        Route::post('locations/create-local-body', [AdminLocationController::class, 'storeLocalBody'])->name('locations.store-local-body');
        Route::get('locations/edit-state/{state}', [AdminLocationController::class, 'editState'])->name('locations.edit-state');
        Route::put('locations/edit-state/{state}', [AdminLocationController::class, 'updateState'])->name('locations.update-state');
        Route::get('locations/edit-district/{district}', [AdminLocationController::class, 'editDistrict'])->name('locations.edit-district');
        Route::put('locations/edit-district/{district}', [AdminLocationController::class, 'updateDistrict'])->name('locations.update-district');
        Route::get('locations/edit-local-body/{localBody}', [AdminLocationController::class, 'editLocalBody'])->name('locations.edit-local-body');
        Route::put('locations/edit-local-body/{localBody}', [AdminLocationController::class, 'updateLocalBody'])->name('locations.update-local-body');
        Route::delete('locations/delete-state/{state}', [AdminLocationController::class, 'destroyState'])->name('locations.destroy-state');
        Route::delete('locations/delete-district/{district}', [AdminLocationController::class, 'destroyDistrict'])->name('locations.destroy-district');
        Route::delete('locations/delete-local-body/{localBody}', [AdminLocationController::class, 'destroyLocalBody'])->name('locations.destroy-local-body');
        Route::get('locations/districts-by-state', [AdminLocationController::class, 'getDistrictsByState'])->name('locations.districts-by-state');
        
        // Ground Management Routes
        Route::resource('grounds', AdminGroundController::class);
        Route::post('grounds/{ground}/toggle-availability', [AdminGroundController::class, 'toggleAvailability'])->name('grounds.toggle-availability');
        Route::get('grounds/districts-by-state', [AdminGroundController::class, 'getDistrictsByState'])->name('grounds.districts-by-state');
        Route::get('grounds/local-bodies-by-district', [AdminGroundController::class, 'getLocalBodiesByDistrict'])->name('grounds.local-bodies-by-district');
        
        // Player Management Routes
        Route::get('players', [\App\Http\Controllers\Admin\PlayerController::class, 'index'])->name('players.index');
        Route::get('players/export/{format}', [\App\Http\Controllers\Admin\PlayerController::class, 'export'])
            ->name('players.export');
        Route::post('players/{player:slug}/reset-pin', [\App\Http\Controllers\Admin\PlayerController::class, 'resetPin'])->name('players.reset-pin');
        Route::post('players/{player:slug}/update-photo', [\App\Http\Controllers\Admin\PlayerController::class, 'updatePhoto'])->name('players.update-photo');
        
        // Documents & printable assets
        Route::get('documents', [AdminDocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/players/{player:slug}', [AdminDocumentController::class, 'showPlayerCard'])->name('documents.players.show');
        Route::get('documents/players/{player:slug}/download', [AdminDocumentController::class, 'downloadPlayerCard'])->name('documents.players.download');
        
        // Analytics Routes
        Route::get('analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/player-registrations', [AdminAnalyticsController::class, 'playerRegistrations'])->name('analytics.player-registrations');
        Route::get('analytics/team-creations', [AdminAnalyticsController::class, 'teamCreations'])->name('analytics.team-creations');
        Route::get('analytics/league-creations', [AdminAnalyticsController::class, 'leagueCreations'])->name('analytics.league-creations');
        Route::get('analytics/auction-completions', [AdminAnalyticsController::class, 'auctionCompletions'])->name('analytics.auction-completions');
        
        // Admin User Management Routes
        Route::resource('admin-users', AdminAdminUserController::class);
        Route::post('admin-users/{adminUser:slug}/reset-pin', [AdminAdminUserController::class, 'resetPin'])->name('admin-users.reset-pin');
        Route::post('admin-users/{adminUser:slug}/update-photo', [AdminAdminUserController::class, 'updatePhoto'])->name('admin-users.update-photo');
        
        // Auction Panel Management Routes
        Route::get('auction-panel', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'index'])->name('auction-panel.index');
        Route::post('auction-panel/league/{league}/grant', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'grantAccess'])->name('auction-panel.grant');
        Route::post('auction-panel/league/{league}/revoke', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'revokeAccess'])->name('auction-panel.revoke');
        Route::post('auction-panel/bulk-grant', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'bulkGrantAccess'])->name('auction-panel.bulk-grant');
        Route::post('auction-panel/bulk-revoke', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'bulkRevokeAccess'])->name('auction-panel.bulk-revoke');
        Route::get('auction-panel/league/{league}/details', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'getLeagueDetails'])->name('auction-panel.league-details');
        Route::get('auction-panel/stats', [\App\Http\Controllers\Admin\AuctionPanelController::class, 'getStats'])->name('auction-panel.stats');
        
        // Auctioneer Management Routes
        Route::get('auctioneers', [\App\Http\Controllers\Admin\AuctioneerManagementController::class, 'index'])->name('auctioneers.index');
        Route::get('auctioneers/leagues/{league}', [\App\Http\Controllers\Admin\AuctioneerManagementController::class, 'show'])->name('auctioneers.show');
        Route::post('auctioneers/leagues/{league}/teams/{leagueTeam}/assign', [\App\Http\Controllers\Admin\AuctioneerManagementController::class, 'assign'])->name('auctioneers.assign');
        Route::delete('auctioneers/leagues/{league}/teams/{leagueTeam}/remove', [\App\Http\Controllers\Admin\AuctioneerManagementController::class, 'remove'])->name('auctioneers.remove');
        Route::get('auctioneers/leagues/{league}/stats', [\App\Http\Controllers\Admin\AuctioneerManagementController::class, 'stats'])->name('auctioneers.stats');
        
        // League Management Routes
        Route::get('leagues', [\App\Http\Controllers\Admin\LeagueController::class, 'index'])->name('leagues.index');
        Route::get('leagues/search-users', [\App\Http\Controllers\Admin\LeagueController::class, 'searchUsers'])->name('leagues.search-users');
        Route::get('leagues/districts-by-state', [\App\Http\Controllers\Admin\LeagueController::class, 'getDistrictsByState'])->name('leagues.districts-by-state');
        Route::get('leagues/local-bodies-by-district', [\App\Http\Controllers\Admin\LeagueController::class, 'getLocalBodiesByDistrict'])->name('leagues.local-bodies-by-district');
        Route::post('leagues/{league:slug}/organizers', [\App\Http\Controllers\Admin\LeagueController::class, 'addOrganizer'])->name('leagues.add-organizer');
        Route::delete('leagues/{league:slug}/organizers/{user:slug}', [\App\Http\Controllers\Admin\LeagueController::class, 'removeOrganizer'])->name('leagues.remove-organizer');
        Route::get('leagues/{league}', [\App\Http\Controllers\Admin\LeagueController::class, 'show'])->name('leagues.show');
        Route::get('leagues/{league}/flow', [\App\Http\Controllers\Admin\LeagueController::class, 'flow'])->name('leagues.flow');
        Route::get('leagues/{league}/edit', [\App\Http\Controllers\Admin\LeagueController::class, 'edit'])->name('leagues.edit');
        Route::put('leagues/{league}', [\App\Http\Controllers\Admin\LeagueController::class, 'update'])->name('leagues.update');
        Route::patch('leagues/{league}/status', [\App\Http\Controllers\Admin\LeagueController::class, 'updateStatus'])->name('leagues.update-status');
        Route::post('leagues/{league}/restart', [\App\Http\Controllers\Admin\LeagueController::class, 'restart'])->name('leagues.restart');
        Route::delete('leagues/{league}', [\App\Http\Controllers\Admin\LeagueController::class, 'destroy'])->name('leagues.destroy');
        Route::get('league-players', [\App\Http\Controllers\Admin\LeaguePlayerController::class, 'index'])->name('league-players.index');
        
        // Team Management Routes in Admin Panel
        Route::get('teams', [\App\Http\Controllers\Admin\TeamController::class, 'index'])->name('teams.index');
        Route::get('teams/{team}/edit', [\App\Http\Controllers\Admin\TeamController::class, 'edit'])->name('teams.edit');
        Route::put('teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'update'])->name('teams.update');
        Route::delete('teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'destroy'])->name('teams.destroy');
        Route::post('teams/{team}/upload-logo', [\App\Http\Controllers\Admin\TeamController::class, 'uploadLogo'])->name('teams.upload-logo');
        Route::post('teams/{team}/upload-banner', [\App\Http\Controllers\Admin\TeamController::class, 'uploadBanner'])->name('teams.upload-banner');
        Route::delete('teams/{team}/remove-logo', [\App\Http\Controllers\Admin\TeamController::class, 'removeLogo'])->name('teams.remove-logo');
        Route::delete('teams/{team}/remove-banner', [\App\Http\Controllers\Admin\TeamController::class, 'removeBanner'])->name('teams.remove-banner');
    });

    // Auction access request route (placed before resource routes to avoid conflicts)
    Route::post('leagues/{league}/request-auction-access', [LeagueController::class, 'requestAuctionAccess'])->name('leagues.request-auction-access')->middleware('auth');
    
    // Leagues resource routes - CRUD only for organizers, viewing for everyone
    Route::get('leagues', [LeagueController::class, 'index'])->name('leagues.index')->middleware('league.viewer');
    Route::get('leagues/create', [LeagueController::class, 'create'])->name('leagues.create')->middleware('league.organizer');
    Route::post('leagues', [LeagueController::class, 'store'])->name('leagues.store')->middleware('league.organizer');
    Route::get('leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show')->middleware('league.viewer');
    Route::get('leagues/{league}/edit', [LeagueController::class, 'edit'])->name('leagues.edit')->middleware('league.organizer');
    Route::put('leagues/{league}', [LeagueController::class, 'update'])->name('leagues.update')->middleware('league.organizer');
    Route::delete('leagues/{league}', [LeagueController::class, 'destroy'])->name('leagues.destroy')->middleware('league.organizer');
    Route::get('leagues/player/broadcast', [LeagueController::class, 'playerBroadcast'])->name('leagues.player.broadcast');
    Route::post('leagues/{league}/set-default', [LeagueController::class, 'setDefault'])->name('leagues.setDefault')->middleware('league.organizer');
    Route::post('leagues/{league}/bid-increments', [LeagueController::class, 'updateBidIncrements'])->name('leagues.update-bid-increments')->middleware('league.organizer');
    Route::post('leagues/{league}/upload-logo', [LeagueController::class, 'uploadLogo'])->name('leagues.upload-logo')->middleware('league.organizer');
    Route::post('leagues/{league}/upload-banner', [LeagueController::class, 'uploadBanner'])->name('leagues.upload-banner')->middleware('league.organizer');
    Route::delete('leagues/{league}/remove-logo', [LeagueController::class, 'removeLogo'])->name('leagues.remove-logo')->middleware('league.organizer');
    Route::delete('leagues/{league}/remove-banner', [LeagueController::class, 'removeBanner'])->name('leagues.remove-banner')->middleware('league.organizer');
    Route::post('leagues/{league}/toggle-auction', [LeagueController::class, 'toggleAuctionStatus'])->name('leagues.toggle-auction')->middleware('league.organizer');
    Route::post('leagues/{league}/set-winner-runner', [LeagueController::class, 'setWinnerRunner'])->name('leagues.set-winner-runner')->middleware('league.organizer');

    // League Teams routes - CRUD only for organizers, viewing for everyone
    Route::resource('leagues.league-teams', LeagueTeamController::class)->except(['show'])->middleware('league.organizer');
    Route::get('leagues/{league}/teams', [LeagueTeamController::class, 'index'])->name('league-teams.index')->middleware('league.viewer');
    Route::get('leagues/{league}/teams/create', [LeagueTeamController::class, 'create'])->name('league-teams.create')->middleware('league.organizer');
    Route::get('leagues/{league}/manage-teams', [LeagueTeamController::class, 'manageTeams'])->name('league-teams.manage')->middleware('team.owner');
    Route::get('leagues/{league}/teams/{leagueTeam}', [LeagueTeamController::class, 'show'])->name('league-teams.show')->middleware('league.viewer');
    Route::post('leagues/{league}/teams', [LeagueTeamController::class, 'store'])->name('league-teams.store')->middleware('league.organizer');
    Route::get('leagues/{league}/teams/{leagueTeam}/edit', [LeagueTeamController::class, 'edit'])->name('league-teams.edit')->middleware('league.organizer');
    Route::put('leagues/{league}/teams/{leagueTeam}', [LeagueTeamController::class, 'update'])->name('league-teams.update')->middleware('league.organizer');
    Route::delete('leagues/{league}/teams/{leagueTeam}', [LeagueTeamController::class, 'destroy'])->name('league-teams.destroy')->middleware('league.organizer');
    Route::post('leagues/{league}/teams/{leagueTeam}/replace', [LeagueTeamController::class, 'replace'])->name('league-teams.replace')->middleware('league.organizer');
    Route::patch('leagues/{league}/teams/{leagueTeam}/status', [LeagueTeamController::class, 'updateStatus'])->name('league-teams.updateStatus')->middleware('league.organizer');
    Route::patch('leagues/{league}/teams/{leagueTeam}/wallet', [LeagueTeamController::class, 'updateWallet'])->name('league-teams.updateWallet')->middleware('league.organizer');

    // League Players routes
    Route::get('leagues/{league}/players', [LeaguePlayerController::class, 'index'])->name('league-players.index')->middleware('league.viewer');
    Route::get('leagues/{league}/players/create', [LeaguePlayerController::class, 'create'])->name('league-players.create')->middleware('league.organizer');
    Route::get('leagues/{league}/players/bulk-create', [LeaguePlayerController::class, 'bulkCreate'])->name('league-players.bulk-create')->middleware('league.organizer');
    Route::post('leagues/{league}/players/bulk-store', [LeaguePlayerController::class, 'bulkStore'])->name('league-players.bulk-store')->middleware('league.organizer');
    Route::post('leagues/{league}/players/bulk-base-price', [LeaguePlayerController::class, 'bulkUpdateBasePrice'])->name('league-players.bulk-base-price')->middleware('league.organizer');
    Route::post('leagues/{league}/players', [LeaguePlayerController::class, 'store'])->name('league-players.store')->middleware('league.organizer');
    Route::get('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'show'])->name('league-players.show')->middleware('league.viewer');
    Route::get('leagues/{league}/players/{leaguePlayer}/edit', [LeaguePlayerController::class, 'edit'])->name('league-players.edit')->middleware('league.organizer');
    Route::put('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'update'])->name('league-players.update')->middleware('league.organizer');
    Route::patch('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'update'])->name('league-players.patch')->middleware('league.organizer');
    Route::delete('leagues/{league}/players/{leaguePlayer}', [LeaguePlayerController::class, 'destroy'])->name('league-players.destroy')->middleware('league.organizer');
    Route::patch('leagues/{league}/players/{leaguePlayer}/status', [LeaguePlayerController::class, 'updateStatus'])->name('league-players.updateStatus')->middleware('league.organizer');
    Route::match(['post', 'patch'], 'leagues/{league}/players/bulk-status', [LeaguePlayerController::class, 'bulkUpdateStatus'])->name('league-players.bulkStatus')->middleware('league.organizer');
    Route::post('leagues/{league}/players/{leaguePlayer}/toggle-retention', [LeaguePlayerController::class, 'toggleRetention'])->name('league-players.toggle-retention')->middleware('team.owner');
    Route::post('leagues/{league}/players/add-retention', [LeaguePlayerController::class, 'addRetentionPlayer'])->name('league-players.add-retention')->middleware('team.owner');

    // Auction routes - organizers manage, team owners and auctioneers can bid
    Route::prefix('leagues/{league}/auction')->name('auction.')->group(function () {
        Route::get('/', [AuctionController::class, 'index'])->name('index')->middleware('live.auction:view');
        Route::get('control-room', [AuctionController::class, 'controlRoom'])->name('control-room')->middleware(['auth', 'league.organizer']);
        Route::post('place-bid', [AuctionController::class, 'placeBid'])->name('place-bid')->middleware('live.auction:auctioneer');
        Route::post('accept-bid', [AuctionController::class, 'acceptBid'])->name('accept-bid')->middleware('live.auction:organizer');
        Route::post('complete', [LeagueController::class, 'completeAuction'])->name('complete')->middleware('live.auction:organizer');
        Route::post('reset', [LeagueController::class, 'resetAuction'])->name('reset')->middleware('live.auction:organizer');
        Route::post('skip-player', [AuctionController::class, 'skipPlayer'])->name('skip-player')->middleware('live.auction:organizer');
        Route::post('current-bids', [AuctionController::class, 'getCurrentBids'])->name('current-bids')->middleware('live.auction:view');
    });

    Route::prefix('auction')->name('auction.')->group(function () {
        // Global auction management routes (no league parameter needed)
        Route::post('call', [AuctionController::class, 'call'])->name('call');
        Route::post('sold', [AuctionController::class, 'sold'])->name('sold');
        Route::post('unsold', [AuctionController::class, 'unsold'])->name('unsold');
        Route::get('search-players', [AuctionController::class, 'searchPlayers'])->name('search-players');
        
        // League-specific auction routes
        Route::get('recent-bids/{league}', [AuctionController::class, 'getRecentBids'])->name('recent-bids')->middleware('live.auction:view');
        Route::get('team-balances/{league}', [AuctionController::class, 'getTeamBalances'])->name('team-balances')->middleware('live.auction:view');
        Route::get('access/{league}', [AuctionController::class, 'getAuctionAccess'])->name('access')->middleware('live.auction:view');
    });

    // Poster routes
    Route::get('posters', [\App\Http\Controllers\PosterController::class, 'listAll'])->name('posters.list');
    Route::get('leagues/{league}/teams/{leagueTeam}/poster', [\App\Http\Controllers\PosterController::class, 'show'])->name('posters.show');

    // League Finance routes - organizers manage finances
    Route::prefix('leagues/{league}')->name('league-finances.')->middleware('league.organizer')->group(function () {
        Route::get('finances', [LeagueFinanceController::class, 'index'])->name('index');
        Route::get('finances/create', [LeagueFinanceController::class, 'create'])->name('create');
        Route::post('finances', [LeagueFinanceController::class, 'store'])->name('store');
        Route::post('finances/quick-income', [LeagueFinanceController::class, 'quickIncome'])->name('quick-income');
        Route::post('finances/individual-team-income', [LeagueFinanceController::class, 'individualTeamIncome'])->name('individual-team-income');
        Route::get('team-payment-status/{teamId}', [LeagueFinanceController::class, 'getTeamPaymentStatus'])->name('team-payment-status');
        Route::get('finances/{finance}', [LeagueFinanceController::class, 'show'])->name('show');
        Route::get('finances/{finance}/edit', [LeagueFinanceController::class, 'edit'])->name('edit');
        Route::put('finances/{finance}', [LeagueFinanceController::class, 'update'])->name('update');
        Route::delete('finances/{finance}', [LeagueFinanceController::class, 'destroy'])->name('destroy');
        Route::get('finances-report', [LeagueFinanceController::class, 'report'])->name('report');
    });

    // League match setup routes - organizers manage fixtures
    Route::prefix('leagues/{league}')->name('leagues.')->middleware('league.organizer')->group(function () {
        Route::get('league-match', [\App\Http\Controllers\LeagueMatchController::class, 'index'])->name('league-match');
        Route::post('league-match/groups', [\App\Http\Controllers\LeagueMatchController::class, 'createGroups'])->name('league-match.groups');
        Route::post('league-match/fixtures', [\App\Http\Controllers\LeagueMatchController::class, 'generateFixtures'])->name('league-match.fixtures');
        Route::get('league-match/fixture-setup', [\App\Http\Controllers\LeagueMatchController::class, 'fixtureSetup'])->name('league-match.fixture-setup');
        Route::post('fixtures', [\App\Http\Controllers\LeagueMatchController::class, 'createFixture'])->name('fixtures.create');
        Route::patch('fixtures/{fixture}/update', [\App\Http\Controllers\LeagueMatchController::class, 'updateFixture'])->name('fixtures.update');
        Route::get('fixtures/pdf', [\App\Http\Controllers\LeagueMatchController::class, 'exportPdf'])->name('fixtures.pdf');
        Route::get('fixtures', [\App\Http\Controllers\LeagueMatchController::class, 'fixtures'])->name('fixtures');
    });
});

// API route for local bodies by district
Route::get('api/local-bodies', [RegisterController::class, 'getLocalBodiesByDistrict'])->name('api.local-bodies');

// API route for league positions
Route::get('api/leagues/{league}/positions', function(\App\Models\League $league) {
    return response()->json([
        'positions' => $league->game->roles->map(function($role) {
            return [
                'id' => $role->id,
                'name' => $role->name
            ];
        })
    ]);
})->name('api.league-positions');

// Public league teams page (at end to avoid catching admin routes)
Route::get('{league}/teams', [LeagueController::class, 'publicTeams'])->name('leagues.public-teams');

// Public league players page (at end to avoid catching admin routes)
Route::get('{league}/players', function (\App\Models\League $league) {
    return redirect()->route('teams.league-players', ['league' => $league->slug]);
})->name('leagues.public-players');
