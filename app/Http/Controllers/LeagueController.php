<?php

namespace App\Http\Controllers;

use App\Events\LeagueAuctionStarted;
use App\Events\PlayerViewedBroadcastEvent;
use App\Models\Game;
use App\Models\Ground;
use App\Models\League;
use App\Models\LocalBody;
use App\Models\State;
use App\Models\District;
use App\Models\LeaguePlayer;
use App\Models\User;
use App\Models\LeagueFinance;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class LeagueController
{
    /**
     * Display a listing of the leagues.
     */
    public function index(): View
    {
        $leagues = League::whereHas('organizers', function($query) {
            $query->where('status', 'approved');
        })->with(['game', 'approvedOrganizers', 'localBody.district', 'leagueTeams', 'leaguePlayers', 'grounds'])->paginate(12);
        return view('leagues.index', compact('leagues'));
    }

    /**
     * Show the form for creating a new league.
     */
    public function create(): View
    {
        $games = Game::where('active', true)->get();
        $grounds = Ground::where('is_available', true)->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $localBodies = LocalBody::orderBy('name')->get();
        return view('leagues.create', compact('games', 'grounds', 'states', 'districts', 'localBodies'));
    }

    /**
     * Store a newly created league in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate(League::rules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // If this is the first league or is_default is checked, make it default
        if ($request->has('is_default') || League::count() === 0) {
            // First, unset all other defaults
            League::where('is_default', true)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoFilename = 'league_logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('leagues/logos', $logoFilename, 'public');
            $validated['logo'] = $logoPath;
        }
        
        // Handle banner upload
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerFilename = 'league_banner_' . time() . '.' . $banner->getClientOriginalExtension();
            $bannerPath = $banner->storeAs('leagues/banners', $bannerFilename, 'public');
            $validated['banner'] = $bannerPath;
        }
        
        $league = League::create($validated);

        // Add the current user as a pending organizer (requires approval)
        $league->organizers()->attach(Auth::id(), [
            'status' => 'pending',
            'message' => 'League creator requesting organizer role',
            'admin_notes' => null
        ]);

        // Also create an organizer request for admin review
        \App\Models\OrganizerRequest::create([
            'user_id' => Auth::id(),
            'league_id' => $league->id,
            'message' => 'I created this league and would like to organize it.',
            'status' => 'pending',
        ]);

        if ($request->has('ground_ids') && is_array($request->ground_ids)) {
            $league->grounds()->attach($request->ground_ids);
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'League created successfully! Your organizer request is pending admin approval.',
                'redirect' => route('leagues.index')
            ]);
        }
        
        return redirect()->route('leagues.index')
            ->with('success', 'League created successfully! Your organizer request is pending admin approval.');
    }

    /**
     * Display the specified league.
     */
    public function show(League $league): View
    {
        $league->load(['game.roles', 'approvedOrganizers', 'organizers', 'grounds', 'localBody.district', 'leagueTeams.team', 'finances']);
        
        // Get counts for organizer role
        $leagueTeamsCount = $league->leagueTeams()->count();
        $leaguePlayersCount = $league->leaguePlayers()->count();
        $fixturesCount = $league->fixtures()->count();
        
        // Get available teams for ownership (teams not yet in this league)
        $availableTeams = \App\Models\Team::whereDoesntHave('leagueTeams', function($query) use ($league) {
            $query->where('league_id', $league->id);
        })->with(['homeGround', 'localBody', 'primaryOwners'])->get();
        
        // Get player status counts for join link card
        $playerStatusCounts = [
            'total' => $league->leaguePlayers()->count(),
            'available' => $league->leaguePlayers()->where('status', 'available')->count(),
            'sold' => $league->leaguePlayers()->where('status', 'sold')->count(),
            'pending' => $league->leaguePlayers()->where('status', 'pending')->count(),
            'unsold' => $league->leaguePlayers()->where('status', 'unsold')->count(),
        ];
        
        return view('leagues.show', compact('league', 'leagueTeamsCount', 'leaguePlayersCount', 'fixturesCount', 'availableTeams', 'playerStatusCounts'));
    }

    /**
     * Show the form for editing the specified league.
     */
    public function edit(League $league): View
    {
        $games = Game::where('active', true)->get();
        $grounds = Ground::where('is_available', true)->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $localBodies = LocalBody::orderBy('name')->get();
        return view('leagues.edit', compact('league', 'games', 'grounds', 'states', 'districts', 'localBodies'));
    }

    /**
     * Update the specified league in storage.
     */
    public function update(Request $request, League $league): RedirectResponse
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        
        // Check if teams or players exist
        $hasTeams = $league->leagueTeams()->count() > 0;
        $hasPlayers = $league->leaguePlayers()->count() > 0;
        
        // Prevent editing max_teams if teams already added
        if ($hasTeams && $request->max_teams != $league->max_teams) {
            return back()->withErrors(['max_teams' => 'Cannot change max teams after teams have been added. Current teams: ' . $league->leagueTeams()->count()]);
        }
        
        // Prevent editing max_team_players if players already added
        if ($hasPlayers && $request->max_team_players != $league->max_team_players) {
            return back()->withErrors(['max_team_players' => 'Cannot change max players per team after players have been added. Current players: ' . $league->leaguePlayers()->count()]);
        }
        
        // Only admins can change status
        if (!$isAdmin && $request->has('status') && $request->status != $league->status) {
            return back()->withErrors(['status' => 'Only admins can change league status.']);
        }
        
        $validated = $request->validate(League::rules());

        // Handle default league setting
        if ($request->has('is_default') && $request->is_default) {
            // First, unset all other defaults
            League::where('is_default', true)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } elseif ($league->is_default && !$request->has('is_default')) {
            // Don't allow unsetting the default if this was the default league
            // unless another one is made default
            $validated['is_default'] = true;
        }
        // Store old values for comparison
        $oldWinnerPrize = $league->winner_prize;
        $oldRunnerPrize = $league->runner_prize;

        $league->update($validated);

        // Sync selected grounds (many-to-many)
        if ($request->has('ground_ids') && is_array($request->ground_ids)) {
            $league->grounds()->sync($request->ground_ids);
        } else {
            // If no grounds selected, detach all
            $league->grounds()->detach();
        }

        // Handle automatic expense creation for cash prizes
        $this->handleCashPrizeExpenses($league, $oldWinnerPrize, $oldRunnerPrize);

        return redirect()->route('leagues.index')
            ->with('success', 'League updated successfully!');
    }

    /**
     * Toggle auction live status (organizer only).
     */
    public function toggleAuctionStatus(League $league): JsonResponse
    {
        // Check if user is an organizer for this league or is an admin
        if (!Auth::user()->isOrganizerForLeague($league->id) && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to toggle auction status.'
            ], 403);
        }

        try {
            $newStatus = !$league->auction_active;
            $league->update(['auction_active' => $newStatus]);

            // If starting auction, set the start time
            if ($newStatus && !$league->auction_started_at) {
                $league->update(['auction_started_at' => now()]);
            }

            // If stopping auction, set the end time
            if (!$newStatus && $league->auction_started_at && !$league->auction_ended_at) {
                $league->update(['auction_ended_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Auction started successfully!' : 'Auction stopped successfully!',
                'auction_active' => $newStatus,
                'auction_status' => $newStatus ? 'live' : 'stopped'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle auction status. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified league from storage.
     */
    public function destroy(League $league): RedirectResponse
    {
        $league->delete();
        return redirect()->route('leagues.index')
            ->with('success', 'League deleted successfully!');
    }

    /**
     * Set a league as the default active league.
     */
    public function setDefault(League $league): RedirectResponse
    {
        // First, unset all defaults
        League::where('is_default', true)->update(['is_default' => false]);
        // Set this league as default
        $league->update(['is_default' => true]);

        return redirect()->route('leagues.index')
            ->with('success', 'Default league updated successfully!');
    }

    /**
     * Update bid increments for a league.
     */
    public function updateBidIncrements(Request $request, League $league): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->json()->all();

            // Log the received data for debugging
            \Log::info('Bid increment update request:', $data);

            $updateData = [
                'bid_increment_type' => $data['bid_increment_type']
            ];

            if ($data['bid_increment_type'] === 'custom') {
                $updateData['custom_bid_increment'] = $data['custom_bid_increment'];
            } else {
                $updateData['predefined_increments'] = $data['predefined_increments'];
            }

            // Log the update data
            \Log::info('Updating league with data:', $updateData);

            $league->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Bid increments updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating bid increments:', [
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating bid increments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function playerBroadcast(Request $request)
    {
        $player_id = $request->player_id;
        $player = LeaguePlayer::find($player_id);

        return response()->json(['status' => 'success', 'data' => $player]);
    }

    /**
     * Show the join link page for a league.
     */
    public function showJoinLink(League $league): View
    {
        $league->load(['game', 'localBody.district', 'approvedOrganizers']);
        
        // Check if user is already registered in this league
        $isAlreadyRegistered = false;
        $playerStatus = null;
        
        if (Auth::check()) {
            $existingPlayer = LeaguePlayer::where('user_id', Auth::id())
                ->where('league_id', $league->id)
                ->first();
            
            if ($existingPlayer) {
                $isAlreadyRegistered = true;
                $playerStatus = $existingPlayer->status;
            }
        }
        
        return view('leagues.join-link', compact('league', 'isAlreadyRegistered', 'playerStatus'));
    }

    /**
     * Process the join link request.
     */
    public function processJoinLink(Request $request, League $league): RedirectResponse
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Store the league info in session for after registration
            session(['join_league_after_registration' => $league->slug]);
            return redirect()->route('register')
                ->with('info', 'Please register first to join the league.');
        }

        // Check if already registered
        $existingPlayer = LeaguePlayer::where('user_id', Auth::id())
            ->where('league_id', $league->id)
            ->first();

        if ($existingPlayer) {
            return redirect()->route('leagues.join-link', $league)
                ->with('info', 'You are already registered in this league.');
        }

        // Register the player in the league
        LeaguePlayer::create([
            'user_id' => Auth::id(),
            'league_id' => $league->id,
            'status' => 'available',
            'base_price' => $league->player_reg_fee,
            'retention' => false,
        ]);

        return redirect()->route('leagues.join-link', $league)
            ->with('success', 'You have successfully joined the league!');
    }

    /**
     * Upload and crop league logo
     */
    public function uploadLogo(Request $request, League $league): JsonResponse
    {
        // Log the request for debugging
        \Log::info('Logo upload request', [
            'league_id' => $league->id,
            'user_id' => auth()->id(),
            'has_file' => $request->hasFile('logo')
        ]);

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        try {
            $image = $request->file('logo');
            $filename = 'league_logo_' . $league->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Store the image
            $path = $image->storeAs('leagues/logos', $filename, 'public');
            
            // Update league with logo path
            $league->update(['logo' => $path]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully',
                'logo_url' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            \Log::error('Logo upload failed', [
                'error' => $e->getMessage(),
                'league_id' => $league->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload and crop league banner
     */
    public function uploadBanner(Request $request, League $league): JsonResponse
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            $image = $request->file('banner');
            $filename = 'league_banner_' . $league->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Store the image
            $path = $image->storeAs('leagues/banners', $filename, 'public');
            
            // Update league with banner path
            $league->update(['banner' => $path]);
            
            return response()->json([
                'success' => true,
                'message' => 'Banner uploaded successfully',
                'banner_url' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload banner: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove league logo
     */
    public function removeLogo(League $league): JsonResponse
    {
        try {
            if ($league->logo && Storage::disk('public')->exists($league->logo)) {
                Storage::disk('public')->delete($league->logo);
            }
            
            $league->update(['logo' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logo removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove logo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove league banner
     */
    public function removeBanner(League $league): JsonResponse
    {
        try {
            if ($league->banner && Storage::disk('public')->exists($league->banner)) {
                Storage::disk('public')->delete($league->banner);
            }
            
            $league->update(['banner' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Banner removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove banner: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete the auction for a league
     */
    public function completeAuction(League $league): RedirectResponse
    {
        // Check if user is authorized to complete auction
        if (!Auth::user()->isOrganizerForLeague($league->id) && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to complete this auction.');
        }

        // Check if auction is currently active
        if ($league->status !== 'active') {
            return redirect()->route('leagues.show', $league)
                ->with('error', 'Auction can only be completed when league is active.');
        }

        // Mark any remaining non-retention players as unsold and finalize league status
        $markedUnsold = 0;
        DB::transaction(function () use ($league, &$markedUnsold) {
            $remainingQuery = $league->leaguePlayers()
                ->where('retention', false)
                ->whereNotIn('status', ['sold', 'unsold']);

            $markedUnsold = (clone $remainingQuery)->count();

            $remainingQuery->update([
                'status' => 'unsold',
                'league_team_id' => null,
                'bid_price' => null,
            ]);

            $league->update([
                'status' => 'auction_completed',
                'auction_active' => false,
                'auction_ended_at' => now(),
            ]);
        });

        // Handle auction completion through service
        app(\App\Services\AuctionAccessService::class)->handleAuctionCompletion($league);

        return redirect()->route('leagues.show', $league)
            ->with('success', 'Auction completed successfully! '
                . ($markedUnsold > 0 ? "{$markedUnsold} remaining players marked as unsold. " : '')
                . 'You can now proceed to match setup.');
    }

    /**
     * Reset the auction for a league (delete all auction and post-auction data)
     */
    public function resetAuction(League $league): RedirectResponse
    {
        // Check if user is authorized to reset auction
        if (!Auth::user()->isOrganizerForLeague($league->id) && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to reset this auction.');
        }

        // Check if league is in a state that allows reset
        if (!in_array($league->status, ['auction_completed', 'active'])) {
            return redirect()->route('leagues.show', $league)
                ->with('error', 'Auction can only be reset when league is active or auction is completed.');
        }

        DB::transaction(function () use ($league) {
            // Delete all auction data related to this league's players
            \App\Models\Auction::whereHas('leaguePlayer', function($query) use ($league) {
                $query->where('league_id', $league->id);
            })->delete();
            
            // Delete all fixtures for this league
            $league->fixtures()->delete();
            
            // Delete all league groups for this league
            $league->leagueGroups()->delete();
            
            // Reset all league players to available status
            $league->leaguePlayers()->update([
                'league_team_id' => null,
                'status' => 'available',
                'bid_price' => null
            ]);
            
            // Reset all league teams wallet balance to original limit
            $league->leagueTeams()->update([
                'wallet_balance' => $league->team_wallet_limit
            ]);
            
            // Reset league status to active
            $league->update([
                'status' => 'active',
                'auction_active' => false,
                'auction_started_at' => null,
                'auction_ended_at' => null
            ]);
        });

        // Log the reset action
        \App\Models\AuctionLog::logAction(
            $league->id,
            Auth::id(),
            'auction_reset',
            'League',
            $league->id,
            ['reset_by' => Auth::user()->name]
        );

        return redirect()->route('leagues.show', $league)
            ->with('success', 'Auction reset successfully! All auction data has been cleared and league is ready for a fresh auction.');
    }

    /**
     * Handle automatic expense creation for cash prizes when they are updated.
     */
    private function handleCashPrizeExpenses(League $league, $oldWinnerPrize, $oldRunnerPrize)
    {
        // Get the "Trophies and Awards" expense category
        $awardsCategory = ExpenseCategory::where('type', 'expense')
            ->where('name', 'Trophies and Awards')
            ->first();

        if (!$awardsCategory) {
            // Fallback to any expense category if "Trophies and Awards" doesn't exist
            $awardsCategory = ExpenseCategory::where('type', 'expense')->first();
        }

        if (!$awardsCategory) {
            return; // No expense category available
        }

        // Handle winner prize changes
        if ($league->winner_prize && $league->winner_prize != $oldWinnerPrize) {
            $this->createOrUpdateCashPrizeExpense(
                $league,
                $awardsCategory,
                'Winner Prize',
                $league->winner_prize,
                'Cash prize for the winning team'
            );
        }

        // Handle runner prize changes
        if ($league->runner_prize && $league->runner_prize != $oldRunnerPrize) {
            $this->createOrUpdateCashPrizeExpense(
                $league,
                $awardsCategory,
                'Runner-up Prize',
                $league->runner_prize,
                'Cash prize for the runner-up team'
            );
        }
    }

    /**
     * Create or update a cash prize expense record.
     */
    private function createOrUpdateCashPrizeExpense(League $league, ExpenseCategory $category, $title, $amount, $description)
    {
        // Check if an expense record already exists for this prize type
        $existingExpense = LeagueFinance::where('league_id', $league->id)
            ->where('type', 'expense')
            ->where('title', 'like', '%' . $title . '%')
            ->first();

        $data = [
            'league_id' => $league->id,
            'user_id' => Auth::id(),
            'expense_category_id' => $category->id,
            'title' => $title . ' - ' . $league->name,
            'description' => $description,
            'amount' => $amount,
            'type' => 'expense',
            'transaction_date' => now(),
        ];

        if ($existingExpense) {
            // Update existing expense
            $existingExpense->update($data);
        } else {
            // Create new expense
            LeagueFinance::create($data);
        }
    }

    /**
     * Request auction access for a league.
     */
    public function requestAuctionAccess(Request $request, League $league): JsonResponse
    {
        // Check if user is an organizer for this league
        if (!auth()->user()->isOrganizerForLeague($league->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to request auction access for this league.'
            ], 403);
        }

        // Check if auction access has already been requested
        if ($league->hasAuctionAccessRequested()) {
            return response()->json([
                'success' => false,
                'message' => 'Auction access has already been requested for this league.'
            ], 400);
        }

        // Check if auction access is already granted
        if ($league->hasAuctionAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Auction access has already been granted for this league.'
            ], 400);
        }

        // Validate league criteria before allowing auction access request
        $errors = [];
        
        // Check if teams are full
        $teamsCount = $league->leagueTeams()->count();
        if ($teamsCount < $league->max_teams) {
            $errors[] = "League must have all {$league->max_teams} teams registered. Currently has {$teamsCount} team(s).";
        }

        // Check if players are full (max_teams * max_team_players)
        $totalPlayersRequired = $league->max_teams * $league->max_team_players;
        $approvedPlayersCount = $league->leaguePlayers()->where('status', '!=', 'pending')->count();
        
        if ($approvedPlayersCount < $totalPlayersRequired) {
            $errors[] = "League must have all {$totalPlayersRequired} players registered and approved. Currently has {$approvedPlayersCount} approved player(s).";
        }

        // Check if all teams have auctioneers assigned
        // Skip teams where:
        // 1. The team has an owner AND
        // 2. That owner has set this team as their default team (they'll bid for themselves)
        $teamsWithoutAuctioneers = $league->leagueTeams()
            ->with('team.owners')
            ->whereNull('auctioneer_id')
            ->get()
            ->filter(function($leagueTeam) {
                // Get the team's owners
                $teamOwners = $leagueTeam->team->owners ?? collect();
                
                // If team has no owners, it needs an auctioneer
                if ($teamOwners->isEmpty()) {
                    return true;
                }
                
                // Check if any owner has this team as their default team
                $hasOwnerWithThisAsDefault = $teamOwners->contains(function($owner) use ($leagueTeam) {
                    return $owner->default_team_id == $leagueTeam->team_id;
                });
                
                // If an owner has this as their default team, they'll be the auctioneer - skip this team
                // Otherwise, this team needs an auctioneer assigned
                return !$hasOwnerWithThisAsDefault;
            })
            ->count();
            
        if ($teamsWithoutAuctioneers > 0) {
            $errors[] = "All teams must have an auctioneer assigned (or have an owner who has set them as their default team). {$teamsWithoutAuctioneers} team(s) are missing auctioneers.";
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'League does not meet auction requirements:',
                'errors' => $errors
            ], 400);
        }

        // Request auction access
        $league->requestAuctionAccess();

        return response()->json([
            'success' => true,
            'message' => 'Auction access request sent successfully! The admin will review your request.'
        ]);
    }

    /**
     * Set winner and runner teams for a league
     */
    public function setWinnerRunner(Request $request, League $league): JsonResponse
    {
        try {
            $validated = $request->validate([
                'winner_team_id' => 'nullable|exists:league_teams,id',
                'runner_team_id' => 'nullable|exists:league_teams,id',
            ]);

            // Check if winner_team_id and runner_team_id belong to this league
            if ($validated['winner_team_id']) {
                $winnerTeam = \App\Models\LeagueTeam::find($validated['winner_team_id']);
                if ($winnerTeam->league_id !== $league->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Winner team does not belong to this league'
                    ], 400);
                }
            }

            if ($validated['runner_team_id']) {
                $runnerTeam = \App\Models\LeagueTeam::find($validated['runner_team_id']);
                if ($runnerTeam->league_id !== $league->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Runner team does not belong to this league'
                    ], 400);
                }
            }

            // Check if winner and runner are different teams
            if ($validated['winner_team_id'] && $validated['runner_team_id'] && 
                $validated['winner_team_id'] === $validated['runner_team_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Winner and runner-up must be different teams'
                ], 400);
            }

            $league->update([
                'winner_team_id' => $validated['winner_team_id'],
                'runner_team_id' => $validated['runner_team_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Winner and runner-up teams set successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set winner and runner: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the shareable public page for a league.
     */
    public function shareable(League $league): View
    {
        // Load all necessary relationships
        $league->load([
            'game',
            'localBody.district',
            'leagueTeams.team',
            'leagueTeams.leaguePlayers.user.position',
            'winnerTeam.team',
            'runnerTeam.team',
            'grounds'
        ]);

        // Get top 3 auction highlights (highest bid prices)
        $topAuctions = $league->leaguePlayers()
            ->where('status', 'sold')
            ->whereNotNull('bid_price')
            ->with(['user.position', 'leagueTeam.team'])
            ->orderBy('bid_price', 'desc')
            ->take(3)
            ->get();

        // Get all teams with their players
        $teams = $league->leagueTeams()
            ->with(['team', 'leaguePlayers.user.position'])
            ->get();

        // Available players ahead of auction
        $availablePlayers = $league->leaguePlayers()
            ->where('status', 'available')
            ->where('retention', false)
            ->with(['user.localBody.district', 'user.position', 'leagueTeam.team'])
            ->orderBy(
                User::select('name')
                    ->whereColumn('users.id', 'league_players.user_id')
            )
            ->get();

        // Get auction statistics
        $auctionStats = [
            'total_players' => $league->leaguePlayers()->count(),
            'sold_players' => $league->leaguePlayers()->where('status', 'sold')->count(),
            'total_spent' => $league->leaguePlayers()
                ->where('status', 'sold')
                ->whereNotNull('bid_price')
                ->sum('bid_price'),
            'average_price' => $league->leaguePlayers()
                ->where('status', 'sold')
                ->whereNotNull('bid_price')
                ->avg('bid_price'),
        ];

        return view('leagues.shareable', compact('league', 'topAuctions', 'teams', 'auctionStats', 'availablePlayers'));
    }

    /**
     * Display public teams page for a league.
     */
    public function publicTeams($leagueSlug): View
    {
        $league = League::where('slug', $leagueSlug)
            ->with(['game', 'leagueTeams.team.owners', 'leagueTeams.leaguePlayers.user.position'])
            ->firstOrFail();
        
        return view('leagues.public-teams', compact('league'));
    }

    /**
     * Display public players page for a league.
     */
    public function publicPlayers(League $league): View
    {
        $league->load([
            'game',
            'leaguePlayers.user.localBody',
            'leaguePlayers.leagueTeam.team',
            'fixtures.homeTeam.team',
            'fixtures.awayTeam.team',
            'localBody',
        ]);
        return view('leagues.players', compact('league'));
    }
}
