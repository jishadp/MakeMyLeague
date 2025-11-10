<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Ground;
use App\Models\LocalBody;
use App\Models\Team;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Role;
use App\Http\Requests\StoreTeamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    /**
     * Display a listing of the teams.
     */
    public function index(Request $request): View
    {
        $query = Team::query()
            ->with(['primaryOwners', 'homeGround', 'localBody']);

        // Filter by local body
        if ($request->has('local_body_id') && $request->local_body_id != '') {
            $query->where('local_body_id', $request->local_body_id);
        }

        // Filter by home ground
        if ($request->has('home_ground_id') && $request->home_ground_id != '') {
            $query->where('home_ground_id', $request->home_ground_id);
        }

        // Sort by name
        if ($request->has('sort_by') && $request->sort_by != '') {
            $sortDirection = $request->has('sort_dir') && $request->sort_dir == 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $teams = $query->paginate(12)->withQueryString();

        // Get all local bodies for filtering
        $localBodies = LocalBody::orderBy('name')->get();

        return view('teams.index', compact('teams', 'localBodies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grounds = Ground::with('localBody')->get();
        $localBodies = LocalBody::with('district')->get();
        return view('teams.create', compact('grounds', 'localBodies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        $validated = $request->validated();

        $team = Team::create([
            'name' => $validated['name'],
            'owner_id' => Auth::id(), // Keep for backward compatibility
            'home_ground_id' => $validated['home_ground_id'],
            'local_body_id' => $validated['local_body_id'],
            'created_by' => Auth::id(),
        ]);

        // Add the current user as the primary owner
        $team->owners()->attach(Auth::id(), [
            'role' => 'owner'
        ]);

        // Automatically assign Team Owner role to the user
        $ownerRole = Role::where('name', User::ROLE_OWNER)->first();
        if ($ownerRole) {
            // Check if user already has this role
            $existingRole = UserRole::where('user_id', Auth::id())
                ->where('role_id', $ownerRole->id)
                ->first();
            
            if (!$existingRole) {
                UserRole::create([
                    'user_id' => Auth::id(),
                    'role_id' => $ownerRole->id,
                ]);
            }
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoFilename = 'team_logo_' . $team->id . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('teams/logos', $logoFilename, 'public');
            $team->logo = $logoPath;
            $team->save();
        }
        
        // Handle banner upload
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerFilename = 'team_banner_' . $team->id . '_' . time() . '.' . $banner->getClientOriginalExtension();
            $bannerPath = $banner->storeAs('teams/banners', $bannerFilename, 'public');
            $team->banner = $bannerPath;
            $team->save();
        }

        // Check if there's a league context and add team to league
        if ($request->has('league_slug')) {
            $league = League::where('slug', $request->league_slug)->firstOrFail();

            // Check if team is not already in this league
            $existingTeam = LeagueTeam::where('team_id', $team->id)
                ->where('league_id', $league->id)
                ->first();

            if (!$existingTeam) {
                $input = [
                    'team_id' => $team->id,
                    'league_id' => $league->id,
                    'wallet_balance' => $league->team_wallet_limit,
                    'created_by' => Auth::id(),
                ];

                if(auth()->user()->isOrganizer()){
                    $input['status'] = 'available';
                }

                LeagueTeam::create($input);

                return redirect()->route('league-teams.index', $league)
                    ->with('success', 'Team created and added to league successfully!');
            }
        }

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team): View
    {
        $team->load(['owner', 'homeGround', 'localBody']);
        return view('teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        // Only allow the team owner or admin to edit
        if (!Auth::user()->isOwnerOfTeam($team->id) && !Auth::user()->isOrganizer()) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'You do not have permission to edit this team.');
        }

        $grounds = Ground::with('localBody')->get();
        $localBodies = LocalBody::with('district')->get();
        return view('teams.edit', compact('team', 'grounds', 'localBodies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        // Only allow the team owner or admin to update
        if (!Auth::user()->isOwnerOfTeam($team->id) && !Auth::user()->isOrganizer()) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'You do not have permission to update this team.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'home_ground_id' => 'required|exists:grounds,id',
            'local_body_id' => 'required|exists:local_bodies,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $team->name = $validated['name'];
        $team->home_ground_id = $validated['home_ground_id'];
        $team->local_body_id = $validated['local_body_id'];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('team-logos', 'public');
            $team->logo = 'storage/' . $logoPath;
        }

        $team->save();

        // Ensure the user who updated the team has the Owner role
        $this->assignOwnerRoleIfNeeded(Auth::user());

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        // Only allow the team owner or admin to delete
        if (!Auth::user()->isOwnerOfTeam($team->id) && !Auth::user()->isOrganizer()) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'You do not have permission to delete this team.');
        }

        // Check if the team is associated with any leagues
        if ($team->leagueTeams()->exists()) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'Cannot delete team. It is associated with one or more leagues.');
        }

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    /**
     * Upload and crop team logo
     */
    public function uploadLogo(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        try {
            $image = $request->file('logo');
            $filename = 'team_logo_' . $team->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Store the image
            $path = $image->storeAs('teams/logos', $filename, 'public');
            
            // Update team with logo path
            $team->update(['logo' => $path]);
            
            // Ensure the user who uploaded the logo has the Owner role
            $this->assignOwnerRoleIfNeeded(Auth::user());
            
            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully',
                'logo_url' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload and crop team banner
     */
    public function uploadBanner(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            $image = $request->file('banner');
            $filename = 'team_banner_' . $team->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Store the image
            $path = $image->storeAs('teams/banners', $filename, 'public');
            
            // Update team with banner path
            $team->update(['banner' => $path]);
            
            // Ensure the user who uploaded the banner has the Owner role
            $this->assignOwnerRoleIfNeeded(Auth::user());
            
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
     * Remove team logo
     */
    public function removeLogo(Team $team): JsonResponse
    {
        try {
            if ($team->logo && Storage::disk('public')->exists($team->logo)) {
                Storage::disk('public')->delete($team->logo);
            }
            
            $team->update(['logo' => null]);
            
            // Ensure the user who removed the logo has the Owner role
            $this->assignOwnerRoleIfNeeded(Auth::user());
            
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
     * Remove team banner
     */
    public function removeBanner(Team $team): JsonResponse
    {
        try {
            if ($team->banner && Storage::disk('public')->exists($team->banner)) {
                Storage::disk('public')->delete($team->banner);
            }
            
            $team->update(['banner' => null]);
            
            // Ensure the user who removed the banner has the Owner role
            $this->assignOwnerRoleIfNeeded(Auth::user());
            
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
     * Display league teams grouped by league.
     */
    public function leagueTeams(Request $request): View
    {
        $query = League::with(['leagueTeams.team.owners', 'game'])
            ->whereHas('leagueTeams')
            ->latest();

        $leagues = $query->paginate(10);
        
        // Get all leagues for sidebar
        $allLeagues = League::whereHas('leagueTeams')
            ->withCount('leagueTeams')
            ->latest()
            ->get();

        return view('teams.league-teams', compact('leagues', 'allLeagues'));
    }

    /**
     * Display league players grouped by league.
     */
    public function leaguePlayers(Request $request): View
    {
        $allLeagues = League::with(['leaguePlayers.user.localBody', 'leaguePlayers.leagueTeam.team', 'game'])
            ->whereHas('leaguePlayers')
            ->withCount('leaguePlayers')
            ->latest()
            ->get();

        $groupedPlayers = [];
        foreach ($allLeagues as $league) {
            $groupedPlayers[$league->id] = $league->leaguePlayers->groupBy(function ($leaguePlayer) {
                return $leaguePlayer->user->localBody->name ?? 'Unknown';
            });
        }

        return view('teams.league-players', compact('allLeagues', 'groupedPlayers'));
    }

    /**
     * Assign Team Owner role to a user if they don't already have it.
     */
    private function assignOwnerRoleIfNeeded(User $user)
    {
        $ownerRole = Role::where('name', User::ROLE_OWNER)->first();
        if ($ownerRole) {
            // Check if user already has this role
            $existingRole = UserRole::where('user_id', $user->id)
                ->where('role_id', $ownerRole->id)
                ->first();
            
            if (!$existingRole) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $ownerRole->id,
                ]);
            }
        }
    }
}
