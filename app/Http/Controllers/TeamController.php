<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Ground;
use App\Models\LocalBody;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Display a listing of the teams.
     */
    public function index(Request $request): View
    {
        $query = Team::query()
            ->with(['owner', 'homeGround', 'localBody']);
        
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'home_ground_id' => 'required|exists:grounds,id',
            'local_body_id' => 'required|exists:local_bodies,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $team = new Team();
        $team->name = $validated['name'];
        // Let the model handle slug generation
        $team->owner_id = Auth::id();
        $team->home_ground_id = $validated['home_ground_id'];
        $team->local_body_id = $validated['local_body_id'];
        $team->created_by = Auth::id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('team-logos', 'public');
            $team->logo = 'storage/' . $logoPath;
        }

        $team->save();

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
        if (Auth::id() !== $team->owner_id && !Auth::user()->isAdmin()) {
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
        if (Auth::id() !== $team->owner_id && !Auth::user()->isAdmin()) {
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

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        // Only allow the team owner or admin to delete
        if (Auth::id() !== $team->owner_id && !Auth::user()->isAdmin()) {
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
}
