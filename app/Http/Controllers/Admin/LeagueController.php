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
        $query = League::with(['game', 'localBody.district.state', 'leagueTeams', 'leaguePlayers', 'approvedOrganizers']);

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

        \DB::transaction(function () use ($league) {
            // Delete auctions first (via league_player_id)
            $leaguePlayerIds = $league->leaguePlayers()->pluck('id');
            if ($leaguePlayerIds->isNotEmpty()) {
                \DB::table('auctions')->whereIn('league_player_id', $leaguePlayerIds)->delete();
            }
            
            // Delete all related data
            $league->leaguePlayers()->delete();
            $league->leagueTeams()->delete();
            $league->fixtures()->delete();
            $league->leagueGroups()->delete();
            $league->finances()->delete();
            \DB::table('league_organizers')->where('league_id', $league->id)->delete();
            \DB::table('team_auctioneers')->where('league_id', $league->id)->delete();
            \DB::table('auction_logs')->where('league_id', $league->id)->delete();
            
            // Delete associated files
            if ($league->logo && Storage::disk('public')->exists($league->logo)) {
                Storage::disk('public')->delete($league->logo);
            }
            if ($league->banner && Storage::disk('public')->exists($league->banner)) {
                Storage::disk('public')->delete($league->banner);
            }

            $league->delete();
        });

        return redirect()->route('admin.leagues.index')
            ->with('success', 'League and all related data deleted successfully!');
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

    /**
     * Show league flow/progress tracking page.
     */
    public function flow(League $league)
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
            'fixtures',
            'finances'
        ]);

        // Get progress tracking data
        $progressStages = $league->getProgressTracking();
        $completionPercentage = $league->getCompletionPercentage();
        $currentStage = $league->getCurrentStage();

        // Calculate individual stats - avoid nested arrays to prevent PHP issues
        $teamsCurrent = $league->leagueTeams->count();
        $teamsRequired = $league->max_teams;
        
        $playersCurrent = $league->leaguePlayers->whereIn('status', ['available', 'sold', 'unsold'])->count();
        $playersRequired = $league->max_teams * $league->max_team_players;
        
        $auctionSold = $league->leaguePlayers->where('status', 'sold')->count();
        $auctionUnsold = $league->leaguePlayers->where('status', 'unsold')->count();
        $auctionAvailable = $league->leaguePlayers->where('status', 'available')->count();
        
        $fixturesTotal = $league->fixtures->count();
        $fixturesCompleted = $league->fixtures->where('status', 'completed')->count();
        
        $financeIncome = $league->finances->where('type', 'income')->count();
        $financeExpenses = $league->finances->where('type', 'expense')->count();
        $financeTotalIncome = $league->finances->where('type', 'income')->sum('amount');
        $financeTotalExpenses = $league->finances->where('type', 'expense')->sum('amount');
        
        return view('admin.leagues.flow', compact(
            'league',
            'progressStages',
            'completionPercentage',
            'currentStage',
            'teamsCurrent',
            'teamsRequired',
            'playersCurrent',
            'playersRequired',
            'auctionSold',
            'auctionUnsold',
            'auctionAvailable',
            'fixturesTotal',
            'fixturesCompleted',
            'financeIncome',
            'financeExpenses',
            'financeTotalIncome',
            'financeTotalExpenses'
        ));
    }

    /**
     * Restart league - clean all related data and reset to initial state.
     */
    public function restart(League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        \DB::transaction(function () use ($league) {
            // Get league player IDs before deletion
            $leaguePlayerIds = \DB::table('league_players')->where('league_id', $league->id)->pluck('id');
            
            // Delete auctions
            if ($leaguePlayerIds->isNotEmpty()) {
                \DB::table('auctions')->whereIn('league_player_id', $leaguePlayerIds)->delete();
            }
            
            // Delete team auctioneers first (foreign key)
            \DB::table('team_auctioneers')->where('league_id', $league->id)->delete();
            
            // Delete all related data using raw queries
            \DB::statement('DELETE FROM league_players WHERE league_id = ?', [$league->id]);
            \DB::statement('DELETE FROM league_teams WHERE league_id = ?', [$league->id]);
            \DB::statement('DELETE FROM fixtures WHERE league_id = ?', [$league->id]);
            \DB::statement('DELETE FROM league_groups WHERE league_id = ?', [$league->id]);
            \DB::statement('DELETE FROM league_finances WHERE league_id = ?', [$league->id]);
            \DB::statement('DELETE FROM league_organizers WHERE league_id = ?', [$league->id]);
            \DB::statement('DELETE FROM auction_logs WHERE league_id = ?', [$league->id]);
            
            // Reset league to fresh state
            $league->update([
                'auction_active' => false,
                'auction_started_at' => null,
                'auction_ended_at' => null,
                'winner_team_id' => null,
                'runner_team_id' => null,
                'status' => 'pending'
            ]);
        });

        return redirect()->route('admin.leagues.index')
            ->with('success', 'League restarted successfully! All teams, players, organizers, and auction data have been cleared.');
    }

    /**
     * Add organizer to league.
     */
    public function addOrganizer(Request $request, League $league)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $league->organizers()->syncWithoutDetaching([
            $request->user_id => ['status' => 'approved', 'admin_notes' => 'Added by admin']
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove organizer from league.
     */
    public function removeOrganizer(League $league, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $league->organizers()->detach($user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Search users for organizer assignment.
     */
    public function searchUsers(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $query = $request->get('query', '');
        
        $users = User::when($query, function($q) use ($query) {
            $q->where(function($subQ) use ($query) {
                $subQ->where('name', 'like', '%' . $query . '%')
                     ->orWhere('email', 'like', '%' . $query . '%')
                     ->orWhere('phone', 'like', '%' . $query . '%');
            });
        })
        ->orderBy('name')
        ->limit(50)
        ->get(['id', 'name', 'email']);

        return response()->json(['users' => $users]);
    }
}

