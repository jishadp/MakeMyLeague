<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class LeagueTeamController extends Controller
{
    use AuthorizesRequests;

    public function create(League $league)
    {
        $this->authorize('update', $league);
        return view('league-teams.create', compact('league'));
    }

    public function store(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
            'owner_name' => 'nullable|string|max:255'
        ]);

        // Check if team already exists in league
        if ($league->teams()->where('team_id', $request->team_id)->exists()) {
            return back()->withErrors(['team_id' => 'This team is already in the league']);
        }

        $league->teams()->attach($request->team_id, [
            'name' => $request->name,
            'owner_id' => null,
            'purse_balance' => 0,
            'initial_purse_balance' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('leagues.show', $league)
            ->with('success', 'Team added to league successfully!');
    }

    public function destroy(League $league, $teamId)
    {
        $this->authorize('update', $league);
        
        $league->teams()->detach($teamId);
        
        return redirect()->route('leagues.show', $league)
            ->with('success', 'Team removed from league successfully!');
    }
}
