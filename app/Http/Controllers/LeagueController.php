<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeagueController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $leagues = Auth::user()->leagues()->latest()->paginate(10);
        return view('leagues.index', compact('leagues'));
    }

    public function create()
    {
        return view('leagues.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $league = Auth::user()->leagues()->create($request->all());

        return redirect()->route('leagues.show', $league)
            ->with('success', 'League created successfully!');
    }

    public function show(League $league)
    {
        $this->authorize('view', $league);
        
        $league->load(['teams', 'players']);
        $availableTeams = Team::whereNotIn('id', $league->teams->pluck('id'))->get();
        $availablePlayers = Player::whereNotIn('id', $league->players->pluck('id'))->get();
        
        return view('leagues.show', compact('league', 'availableTeams', 'availablePlayers'));
    }

    public function edit(League $league)
    {
        $this->authorize('update', $league);
        return view('leagues.edit', compact('league'));
    }

    public function update(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:Pending,Active,Completed'
        ]);

        $league->update($request->all());

        return redirect()->route('leagues.show', $league)
            ->with('success', 'League updated successfully!');
    }

    public function destroy(League $league)
    {
        $this->authorize('delete', $league);
        
        $league->delete();

        return redirect()->route('leagues.index')
            ->with('success', 'League deleted successfully!');
    }

    public function addTeams(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        $request->validate([
            'team_ids' => 'required|array',
            'team_ids.*' => 'exists:teams,id'
        ]);

        $league->teams()->attach($request->team_ids);

        return redirect()->route('leagues.show', $league)
            ->with('success', 'Teams added to league successfully!');
    }

    public function addPlayers(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        $request->validate([
            'player_ids' => 'required|array',
            'player_ids.*' => 'exists:players,id'
        ]);

        $league->players()->attach($request->player_ids);

        return redirect()->route('leagues.show', $league)
            ->with('success', 'Players added to league successfully!');
    }
}
