<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\League;
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
        $leagues = League::with('game', 'organizer')->get();
        return view('leagues.index', compact('leagues'));
    }

    /**
     * Show the form for creating a new league.
     */
    public function create(): View
    {
        $games = Game::where('active', true)->get();
        return view('leagues.create', compact('games'));
    }

    /**
     * Store a newly created league in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(League::rules());
        
        // Add the current authenticated user as the organizer
        $validated['user_id'] = Auth::id();
        
        // If this is the first league or is_default is checked, make it default
        if ($request->has('is_default') || League::count() === 0) {
            // First, unset all other defaults
            League::where('is_default', true)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }
        
        League::create($validated);
        
        return redirect()->route('leagues.index')
            ->with('success', 'League created successfully!');
    }

    /**
     * Display the specified league.
     */
    public function show(League $league): View
    {
        return view('leagues.show', compact('league'));
    }

    /**
     * Show the form for editing the specified league.
     */
    public function edit(League $league): View
    {
        $games = Game::where('active', true)->get();
        return view('leagues.edit', compact('league', 'games'));
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
}
