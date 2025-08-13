<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\LocalBody;
use App\Models\Team;
use Illuminate\Http\Request;
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
     * Display the specified team.
     */
    public function show(Team $team): View
    {
        $team->load(['owner', 'homeGround', 'localBody']);
        return view('teams.show', compact('team'));
    }
}
