<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Game;
use App\Models\State;
use App\Models\District;
use App\Models\LocalBody;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeagueController extends Controller
{
    /**
     * Display the admin league management dashboard.
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Calculate league stats - direct counts from database
        $totalLeagues = \DB::table('leagues')->count();
        $pendingLeagues = \DB::table('leagues')->where('status', 'pending')->count();
        $activeLeagues = \DB::table('leagues')->where('status', 'active')->count();
        $completedLeagues = \DB::table('leagues')->where('status', 'completed')->count();
        $cancelledLeagues = \DB::table('leagues')->where('status', 'cancelled')->count();

        $leagueStats = [
            'total' => $totalLeagues,
            'pending' => $pendingLeagues,
            'active' => $activeLeagues,
            'completed' => $completedLeagues,
            'cancelled' => $cancelledLeagues,
        ];

        // Build query with filters
        $query = League::with(['game', 'localBody.district.state', 'leagueTeams', 'leaguePlayers']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by game
        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $leagues = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());
        $games = Game::orderBy('name')->get();

        return view('admin.leagues.index', [
            'leagues' => $leagues,
            'games' => $games,
            'leagueStats' => $leagueStats
        ]);
    }

    /**
     * Display the specified league.
     */
    public function show(League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $league->load([
            'game',
            'localBody.district.state',
            'leagueTeams.team',
            'leaguePlayers.user',
            'organizers',
            'fixtures'
        ]);

        return view('admin.leagues.show', compact('league'));
    }

    /**
     * Show the form for editing the specified league.
     */
    public function edit(League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $games = Game::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $localBodies = LocalBody::orderBy('name')->get();

        return view('admin.leagues.edit', compact('league', 'games', 'states', 'districts', 'localBodies'));
    }

    /**
     * Update the specified league.
     */
    public function update(Request $request, League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'venue_details' => 'nullable|string',
            'season' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_teams' => 'required|integer|min:2',
            'max_team_players' => 'required|integer|min:1',
            'localbody_id' => 'required|exists:local_bodies,id',
            'team_reg_fee' => 'nullable|numeric|min:0',
            'player_reg_fee' => 'nullable|numeric|min:0',
            'winner_prize' => 'nullable|numeric|min:0',
            'runner_prize' => 'nullable|numeric|min:0',
            'team_wallet_limit' => 'nullable|numeric|min:0',
            'retention' => 'nullable|boolean',
            'retention_players' => 'nullable|integer|min:0',
            'status' => 'required|in:pending,active,completed,cancelled',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($league->logo && Storage::disk('public')->exists($league->logo)) {
                Storage::disk('public')->delete($league->logo);
            }
            $logo = $request->file('logo');
            $logoFilename = 'league_logo_' . $league->id . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('leagues/logos', $logoFilename, 'public');
            $validated['logo'] = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($league->banner && Storage::disk('public')->exists($league->banner)) {
                Storage::disk('public')->delete($league->banner);
            }
            $banner = $request->file('banner');
            $bannerFilename = 'league_banner_' . $league->id . '_' . time() . '.' . $banner->getClientOriginalExtension();
            $bannerPath = $banner->storeAs('leagues/banners', $bannerFilename, 'public');
            $validated['banner'] = $bannerPath;
        }

        $league->update($validated);

        return redirect()->route('admin.leagues.index')
            ->with('success', 'League updated successfully!');
    }

    /**
     * Update league status quickly.
     */
    public function updateStatus(Request $request, League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        $league->update($validated);

        return redirect()->back()
            ->with('success', 'League status updated to ' . $validated['status'] . '!');
    }

    /**
     * Remove the specified league.
     */
    public function destroy(League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if league has teams or players
        if ($league->leagueTeams()->count() > 0 || $league->leaguePlayers()->count() > 0) {
            return redirect()->route('admin.leagues.index')
                ->with('error', 'Cannot delete league. It has registered teams or players.');
        }

        // Delete associated files
        if ($league->logo && Storage::disk('public')->exists($league->logo)) {
            Storage::disk('public')->delete($league->logo);
        }
        if ($league->banner && Storage::disk('public')->exists($league->banner)) {
            Storage::disk('public')->delete($league->banner);
        }

        $league->delete();

        return redirect()->route('admin.leagues.index')
            ->with('success', 'League deleted successfully!');
    }

    /**
     * Get districts by state for AJAX requests.
     */
    public function getDistrictsByState(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $districts = District::where('state_id', $request->state_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    /**
     * Get local bodies by district for AJAX requests.
     */
    public function getLocalBodiesByDistrict(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $localBodies = LocalBody::where('district_id', $request->district_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($localBodies);
    }
}

