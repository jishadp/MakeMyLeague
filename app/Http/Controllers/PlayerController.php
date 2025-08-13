<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('team')->paginate(12);
        return view('players.index', compact('players'));
    }

    public function create()
    {
        $teams = Team::all();
        return view('players.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:16|max:50',
            'team_id' => 'nullable|exists:teams,id'
        ]);

        Player::create($validated);
        return redirect()->route('players.index')->with('success', 'Player created successfully!');
    }

    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        $teams = Team::all();
        return view('players.edit', compact('player', 'teams'));
    }

    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:16|max:50',
            'team_id' => 'nullable|exists:teams,id'
        ]);

        $player->update($validated);
        return redirect()->route('players.index')->with('success', 'Player updated successfully!');
    }

    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully!');
    }
}
