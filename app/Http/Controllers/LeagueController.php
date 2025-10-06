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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $leagues = League::with(['game', 'approvedOrganizers', 'localBody.district', 'leagueTeams', 'leaguePlayers', 'grounds'])->get();
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(League::rules());

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
        $league->update($validated);

        // Sync selected grounds (many-to-many)
        if ($request->has('ground_ids') && is_array($request->ground_ids)) {
            $league->grounds()->sync($request->ground_ids);
        } else {
            // If no grounds selected, detach all
            $league->grounds()->detach();
        }

        return redirect()->route('leagues.index')
            ->with('success', 'League updated successfully!');
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
}
