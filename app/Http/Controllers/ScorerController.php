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
            'fixturePlayers.player.user',
            'penalties',
            'tossWinnerTeam.team'
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
            'match_state' => Fixture::STATE_FIRST_HALF,
            'current_minute' => 0,
            'is_running' => true,
            'last_tick_at' => now(),
            'started_at' => now(),
            'match_duration' => $validated['duration']
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Match Started',
            'fixture' => $fixture->fresh()
        ]);
    }

    public function updateTimer(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);
        
        $action = $request->input('action');
        
        switch ($action) {
            case 'resume':
                $fixture->update(['is_running' => true, 'last_tick_at' => now()]);
                break;
            case 'pause':
                $fixture->update(['is_running' => false, 'last_tick_at' => null]);
                break;
            case 'tick':
                $autoPaused = false;
                $newState = null;
                
                if ($fixture->is_running) {
                    $fixture->increment('current_minute');
                    $fixture->update(['last_tick_at' => now()]);
                    
                    $matchDuration = $fixture->match_duration ?? 45;
                    $halftimeMark = $matchDuration;
                    $fulltimeMark = $matchDuration * 2;
                    
                    // Auto-pause at halftime
                    if ($fixture->match_state === Fixture::STATE_FIRST_HALF && $fixture->current_minute >= $halftimeMark) {
                        $fixture->update([
                            'match_state' => Fixture::STATE_HALF_TIME,
                            'is_running' => false,
                            'last_tick_at' => null
                        ]);
                        $autoPaused = true;
                        $newState = Fixture::STATE_HALF_TIME;
                    }
                    
                    // Auto-pause at fulltime
                    if ($fixture->match_state === Fixture::STATE_SECOND_HALF && $fixture->current_minute >= $fulltimeMark) {
                        $fixture->update([
                            'match_state' => Fixture::STATE_FULL_TIME,
                            'is_running' => false,
                            'last_tick_at' => null
                        ]);
                        $autoPaused = true;
                        $newState = Fixture::STATE_FULL_TIME;
                    }
                }
                
                // Return early with auto_paused info
                return response()->json([
                    'success' => true,
                    'fixture' => $fixture->fresh(),
                    'auto_paused' => $autoPaused,
                    'new_state' => $newState
                ]);
                break;
            case 'set_minute':
                $fixture->update(['current_minute' => (int) $request->input('minute')]);
                break;
            case 'change_state':
                $newState = $request->input('state');
                $updates = ['match_state' => $newState, 'is_running' => false, 'last_tick_at' => null];
                
                // Reset/Set time logic based on state
                if ($newState === Fixture::STATE_SECOND_HALF) {
                    $updates['current_minute'] = 45;
                } elseif ($newState === Fixture::STATE_EXTRA_TIME_FIRST) {
                    $updates['current_minute'] = 90;
                } elseif ($newState === Fixture::STATE_EXTRA_TIME_SECOND) {
                    $updates['current_minute'] = 105;
                }
                
                $fixture->update($updates);
                break;
            case 'add_time': 
                $minutes = (int) $request->input('minutes');
                // If in First Half OR Half Time, update first half added time
                if (in_array($fixture->match_state, [Fixture::STATE_FIRST_HALF, Fixture::STATE_HALF_TIME])) {
                    $fixture->update(['added_time_first_half' => $minutes]);
                } else {
                    $fixture->update(['added_time_second_half' => $minutes]);
                }
                break;
            case 'set_duration':
                $fixture->update(['match_duration' => (int) $request->input('duration')]);
                break;
        }

        return response()->json([
            'success' => true, 
            'fixture' => $fixture->fresh(),
            'match_time' => $fixture->match_time_display
        ]);
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

        // Check if knockout and draw - enable penalties
        $isDraw = $fixture->home_score === $fixture->away_score;
        $isKnockout = $fixture->is_knockout;

        if ($isKnockout && $isDraw && !$fixture->toss_conducted) {
            // Enable penalty mode
            $fixture->update([
                'match_state' => Fixture::STATE_FULL_TIME,
                'is_running' => false,
                'last_tick_at' => null,
                'has_penalties' => true
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Match ended in draw - Penalties required', 
                'fixture' => $fixture->fresh(),
                'requires_penalties' => true
            ]);
        }

        $fixture->update([
            'status' => 'completed',
            'match_state' => Fixture::STATE_FULL_TIME,
            'is_running' => false,
            'last_tick_at' => null
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Match Finished', 
            'fixture' => $fixture->fresh(),
            'requires_penalties' => false
        ]);
    }

    public function storePenalty(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $validated = $request->validate([
            'team_id' => 'required|exists:league_teams,id',
            'player_id' => 'nullable|exists:league_players,id',
            'player_name' => 'nullable|string',
            'scored' => 'required|boolean',
            'attempt_number' => 'required|integer|min:1',
        ]);

        $penalty = $fixture->penalties()->create($validated);

        return response()->json([
            'success' => true,
            'penalty' => $penalty,
            'home_penalty_score' => $fixture->home_penalty_score,
            'away_penalty_score' => $fixture->away_penalty_score
        ]);
    }

    public function updatePenalty(Request $request, Fixture $fixture, $penaltyId)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $penalty = $fixture->penalties()->findOrFail($penaltyId);

        $validated = $request->validate([
            'scored' => 'required|boolean',
        ]);

        $penalty->update($validated);

        return response()->json([
            'success' => true,
            'penalty' => $penalty,
            'home_penalty_score' => $fixture->home_penalty_score,
            'away_penalty_score' => $fixture->away_penalty_score
        ]);
    }

    public function completePenalties(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $validated = $request->validate([
            'winner_team_id' => 'required|exists:league_teams,id',
        ]);

        $fixture->update([
            'status' => 'completed',
            'penalty_winner_team_id' => $validated['winner_team_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penalties completed',
            'fixture' => $fixture->fresh()
        ]);
    }

    public function continueMatch(Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        // Delete all penalties
        $fixture->penalties()->delete();

        // Reset match to in_progress state
        $fixture->update([
            'status' => 'in_progress',
            'match_state' => Fixture::STATE_SECOND_HALF,
            'has_penalties' => false,
            'penalty_winner_team_id' => null,
            'is_running' => false,
            'last_tick_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match resumed - penalties cleared',
            'fixture' => $fixture->fresh()
        ]);
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

    public function conductToss(Request $request, Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $validated = $request->validate([
            'winner_team_id' => 'required|exists:league_teams,id',
        ]);

        // Verify the team is part of this fixture
        if ($validated['winner_team_id'] != $fixture->home_team_id && $validated['winner_team_id'] != $fixture->away_team_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid team selected'
            ], 400);
        }

        $fixture->update([
            'toss_winner_team_id' => $validated['winner_team_id'],
            'toss_conducted' => true,
            'toss_conducted_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Toss recorded successfully',
            'fixture' => $fixture->fresh(['tossWinnerTeam.team'])
        ]);
    }

    public function clearToss(Fixture $fixture)
    {
        $this->authorize('viewScoringConsole', $fixture);

        $fixture->update([
            'toss_winner_team_id' => null,
            'toss_conducted' => false,
            'toss_conducted_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Toss cleared',
            'fixture' => $fixture->fresh()
        ]);
    }
}
