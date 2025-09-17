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
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeagueController
{
    /**
     * Display a listing of the leagues.
     */
    public function index(): View
    {
        $leagues = League::with(['game', 'organizer', 'localBody.district'])->get();
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
        // Add the current authenticated user as the organizer
        $validated['user_id'] = Auth::id();
        // Process ground_ids (convert to JSON array)
        // if ($request->has('ground_ids')) {
        //     $validated['ground_ids'] = $request->ground_ids;
        // }
        // If this is the first league or is_default is checked, make it default
        if ($request->has('is_default') || League::count() === 0) {
            // First, unset all other defaults
            League::where('is_default', true)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }
        $league = League::create($validated);
        return redirect()->route('leagues.index')
            ->with('success', 'League created successfully!');
    }

    /**
     * Display the specified league.
     */
    public function show(League $league): View
    {
        $league->with(['game.roles', 'organizer', 'localBody.district']);
        
        // Get counts for organizer role
        $leagueTeamsCount = $league->leagueTeams()->count();
        $leaguePlayersCount = $league->leaguePlayers()->count();
        
        return view('leagues.show', compact('league', 'leagueTeamsCount', 'leaguePlayersCount'));
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
        // Process ground_ids (convert to JSON array)
        if ($request->has('ground_ids')) {
            $validated['ground_ids'] = $request->ground_ids;
        }
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
}
