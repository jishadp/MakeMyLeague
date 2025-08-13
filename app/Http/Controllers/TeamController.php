<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('players')->paginate(12);
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'country' => 'nullable|string|max:255'
        ]);

        Team::create($validated);
        return redirect()->route('teams.index')->with('success', 'Team created successfully!');
    }

    public function show(Team $team)
    {
        $team->load('players');
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'country' => 'nullable|string|max:255'
        ]);

        $team->update($validated);
        return redirect()->route('teams.index')->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully!');
    }
}
