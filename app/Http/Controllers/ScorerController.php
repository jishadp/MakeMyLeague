<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fixture;
use App\Models\MatchEvent;

class ScorerController extends Controller
{
    public function index(Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        // Load specific relationships needed for the view
        $fixture->load([
            'homeTeam.team', 
            'awayTeam.team',
            'homeTeam.leaguePlayers.user', 
            'awayTeam.leaguePlayers.user', 
            'events.player.user',
            'events.assistPlayer.user',
            'events.relatedPlayer.user',
            'events.team.team',
            'fixturePlayers.player.user'
        ]);
        
        return view('scorer.console', compact('fixture'));
    }

    public function startMatch(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $validated = $request->validate([
            'duration' => 'required|integer|min:1',
            'home_starters' => 'nullable|array',
            'away_starters' => 'nullable|array',
            'home_substitutes' => 'nullable|array', // If we want explicit bench selection
            'home_guests' => 'nullable|array',
            'away_guests' => 'nullable|array',
        ]);

        // Clear existing fixture players to avoid duplicates if restarted
        $fixture->fixturePlayers()->delete();

        // Helper to add players
        $addPlayers = function($teamId, $playerIds, $status, $isActive) use ($fixture) {
            if (empty($playerIds)) return;
            foreach ($playerIds as $pid) {
                // Determine valid status enum
                $statusEnum = $status; 
                // DB enum: 'starting', 'bench', 'subbed_in', 'subbed_out'
                
                $fixture->fixturePlayers()->create([
                    'team_id' => $teamId,
                    'player_id' => $pid,
                    'status' => $statusEnum,
                    'is_active' => $isActive,
                ]);
            }
        };

        // Add Home/Away Starters
        $addPlayers($fixture->home_team_id, $request->input('home_starters', []), 'starting', true);
        $addPlayers($fixture->away_team_id, $request->input('away_starters', []), 'starting', true);

        // Calculate and Add Bench - Automatically for anyone NOT in starters
        // Fetch full roster IDs
        $homeStarters = collect($request->input('home_starters', []));
        $awayStarters = collect($request->input('away_starters', []));
        
        $homeRosterIds = $fixture->homeTeam->leaguePlayers()->pluck('league_players.id'); // Explicit table name just in case
        $awayRosterIds = $fixture->awayTeam->leaguePlayers()->pluck('league_players.id');

        $homeBench = $homeRosterIds->diff($homeStarters);
        $awayBench = $awayRosterIds->diff($awayStarters);

        $addPlayers($fixture->home_team_id, $homeBench->all(), 'bench', false);
        $addPlayers($fixture->away_team_id, $awayBench->all(), 'bench', false);



        // Handle Guests (Name only)
        $addGuests = function($teamId, $names) use ($fixture) {
             if (empty($names)) return;
             foreach ($names as $name) {
                 if(empty($name)) continue;
                 $fixture->fixturePlayers()->create([
                    'team_id' => $teamId,
                    'player_id' => null,
                    'custom_name' => $name,
                    'status' => 'starting', // Assume guests start? Or make UI for bench guests? Let's assume start for simplicity or add to bench if separate input.
                    // User request: "show the selected team list as lineups with quests (as custom added)"
                    // I will treat guests as 'starting' for now unless UI splits them.
                    'is_active' => true, 
                ]);
             }
        };

        $addGuests($fixture->home_team_id, $request->input('home_guests', []));
        $addGuests($fixture->away_team_id, $request->input('away_guests', []));

        $fixture->update([
            'status' => 'in_progress', // LIVE
            'started_at' => now(),
            'match_duration' => $validated['duration']
        ]);

        return response()->json(['success' => true, 'message' => 'Match Started']);
    }

    public function storeEvent(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $validated = $request->validate([
            'event_type' => 'required|string',
            'minute' => 'required|integer',
            'team_id' => 'nullable|exists:league_teams,id',
            'player_id' => 'nullable|exists:league_players,id',
            'player_name' => 'nullable|string',
            'description' => 'nullable|string',
            'assist_player_id' => 'nullable|exists:league_players,id', // Validated assist
            'assist_player_name' => 'nullable|string',
        ]);

        $event = $fixture->events()->create($validated);

        // Update Score if Goal
        if ($validated['event_type'] === 'GOAL') {
            if ($validated['team_id'] == $fixture->home_team_id) {
                // Explicitly update to ensure value is committed
                $fixture->refresh(); 
                $fixture->home_score = ($fixture->home_score ?? 0) + 1;
                $fixture->save();
            } elseif ($validated['team_id'] == $fixture->away_team_id) {
                $fixture->refresh();
                $fixture->away_score = ($fixture->away_score ?? 0) + 1;
                $fixture->save();
            }
        }

        return response()->json([
            'success' => true, 
            'event' => $event,
            'new_scores' => [
                'home' => $fixture->home_score,
                'away' => $fixture->away_score
            ]
        ]);
    }

    public function substitute(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $validated = $request->validate([
            'team_id' => 'required|exists:league_teams,id',
            'player_out_id' => 'nullable|exists:league_players,id', // Nullable for guest? guest handling in subs is tricky without ID.
            'player_in_id' => 'nullable|exists:league_players,id',
            'minute' => 'required|integer',
        ]);
        
        // For guest subs, we might rely on ID being null and checking existing? 
        // Existing logic relies on ID. Guests don't have IDs. 
        // For now, only supporting substitution of REGISTERED players properly. 
        // Guests might just be visual in lineup or one-off.
        
        // Record Event
        $fixture->events()->create([
            'event_type' => 'SUB',
            'minute' => $validated['minute'],
            'team_id' => $validated['team_id'],
            'player_id' => $validated['player_out_id'],
            'related_player_id' => $validated['player_in_id'],
        ]);

        // Update Statuses
        if ($validated['player_out_id']) {
            $fixture->fixturePlayers()
                ->where('player_id', $validated['player_out_id'])
                ->update(['status' => 'subbed_out', 'is_active' => false]);
        }

        if ($validated['player_in_id']) {
            $playerIn = $fixture->fixturePlayers()->where('player_id', $validated['player_in_id'])->first();
            if ($playerIn) {
                $playerIn->update(['status' => 'subbed_in', 'is_active' => true]);
            } else {
                $fixture->fixturePlayers()->create([
                    'team_id' => $validated['team_id'],
                    'player_id' => $validated['player_in_id'],
                    'status' => 'subbed_in',
                    'is_active' => true
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Substitution recorded']);
    }

    public function finishMatch(Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $fixture->update([
            'status' => 'completed',
        ]);

        return response()->json(['success' => true, 'message' => 'Match Finished']);
    }
    public function deleteEvent(Request $request, Fixture $fixture, $eventId)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $event = MatchEvent::findOrFail($eventId);

        if ($event->fixture_id !== $fixture->id) {
            abort(403, 'Event does not belong to this fixture');
        }

        // Revert Logic
        if ($event->event_type === 'GOAL') {
             if ($event->team_id == $fixture->home_team_id) {
                 $fixture->decrement('home_score');
             } elseif ($event->team_id == $fixture->away_team_id) {
                 $fixture->decrement('away_score');
             }
        }

        // Revert Subs
        if ($event->event_type === 'SUB') {
             // Revert Player OUT (make active again)
             if ($event->player_id) {
                 $fixture->fixturePlayers()
                    ->where('player_id', $event->player_id)
                    ->update(['status' => 'starting', 'is_active' => true]); 
             }
             // Revert Player IN (make inactive again)
             if ($event->related_player_id) {
                 $fixture->fixturePlayers()
                    ->where('player_id', $event->related_player_id)
                    ->update(['status' => 'bench', 'is_active' => false]);
             }
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'new_scores' => [
                'home' => $fixture->refresh()->home_score,
                'away' => $fixture->away_score
            ]
        ]);
    }
}
