<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveMatchApiController extends Controller
{
    public function index()
    {
        $leagues = League::whereHas('fixtures', function ($query) {
            $query->whereIn('status', ['in_progress', 'scheduled', 'unscheduled', 'completed']);
        })
        ->whereHas('game', function ($query) {
            $query->where('name', 'FootBall'); // Currently focused on Football as per web logic
        })
        ->with(['fixtures' => function ($query) {
             $query->whereIn('status', ['in_progress', 'scheduled', 'unscheduled']) // Only show relevant fixtures count in listing
                   ->orderBy('match_date', 'asc')
                   ->orderBy('match_time', 'asc');
        }, 'localBody.district'])
        ->get()
        ->map(function ($league) {
            $liveCount = $league->fixtures->where('status', 'in_progress')->count();
            $upcomingCount = $league->fixtures->whereIn('status', ['scheduled', 'unscheduled'])->count();
            
            return [
                'id' => $league->id,
                'name' => $league->name,
                'slug' => $league->slug,
                'logo' => $league->logo ? Storage::url($league->logo) : null,
                'district' => $league->localBody?->district?->name,
                'live_count' => $liveCount,
                'upcoming_count' => $upcomingCount,
                'game_type' => $league->game?->name,
            ];
        });

        return response()->json([
            'success' => true,
            'leagues' => $leagues
        ]);
    }

    public function leagueMatches(League $league)
    {
        $league->load(['fixtures' => function($q) {
            $q->with(['homeTeam.team', 'awayTeam.team']);
        }]);

        $fixtures = $league->fixtures->map(function($match) {
            return [
                'id' => $match->id,
                'slug' => $match->slug,
                'status' => $match->status,
                'match_date' => $match->match_date?->format('Y-m-d'),
                'match_time' => $match->match_time?->format('H:i:s'),
                'formatted_date' => $match->match_date?->format('M d, Y'),
                'formatted_time' => $match->match_time?->format('h:i A'),
                'home_team' => [
                    'name' => $match->homeTeam?->team?->name ?? 'Home',
                    'logo' => $match->homeTeam?->team?->logo ? Storage::url($match->homeTeam->team->logo) : null,
                ],
                'away_team' => [
                    'name' => $match->awayTeam?->team?->name ?? 'Away',
                    'logo' => $match->awayTeam?->team?->logo ? Storage::url($match->awayTeam->team->logo) : null,
                ],
                'home_score' => $match->home_score ?? 0,
                'away_score' => $match->away_score ?? 0,
                'scorer_id' => $match->scorer_id,
                'match_type' => $match->match_type,
            ];
        });

        $liveMatches = $fixtures->where('status', 'in_progress')->values();
        
        $upcomingMatches = $fixtures->whereIn('status', ['scheduled', 'unscheduled'])
            ->sortBy(function($match) {
                return ($match['match_date'] ?? '9999-12-31') . ($match['match_time'] ?? '00:00:00');
            })
            ->values();

        $pastMatches = $fixtures->where('status', 'completed')
             ->sortByDesc(function($match) {
                return ($match['match_date'] ?? '0000-00-00');
            })
            ->values();

        return response()->json([
            'success' => true,
            'league' => [
                'id' => $league->id,
                'name' => $league->name,
                'slug' => $league->slug,
                'logo' => $league->logo ? Storage::url($league->logo) : null,
                'organizer_id' => $league->organizer_id,
            ],
            'live_matches' => $liveMatches,
            'upcoming_matches' => $upcomingMatches,
            'past_matches' => $pastMatches,
        ]);
    }

    public function show(Fixture $fixture)
    {
        $fixture->load([
            'homeTeam.team', 
            'awayTeam.team', 
            'league', 
            'events.player.user', 
            'events.team.team',
            'events.assistPlayer.user',
            'events.relatedPlayer.user',
            'fixturePlayers.player.user'
        ]);

        $events = $fixture->events->map(function($event) {
            return [
                'id' => $event->id,
                'event_type' => $event->event_type,
                'minute' => $event->minute,
                'description' => $event->description,
                'team_id' => $event->team_id, // Add team_id for filtering
                'player_name' => $event->player?->user?->name ?? $event->player_name,
                'player_photo' => $event->player?->user?->photo ? Storage::url($event->player->user->photo) : null,
                'team_logo' => $event->team?->team?->logo ? Storage::url($event->team->team->logo) : null,
                'assist_player_name' => $event->assistPlayer?->user?->name ?? $event->assist_player_name,
                'related_player_name' => $event->relatedPlayer?->user?->name ?? $event->related_player_name,
                'team_name' => $event->team?->team?->name,
            ];
        });

        $lineups = [
            'home' => $fixture->fixturePlayers->where('team_id', $fixture->home_team_id)->map(function($fp) {
                return [
                    'id' => $fp->id,
                    'name' => $fp->player?->user?->name ?? $fp->custom_name ?? 'Guest',
                    'is_active' => $fp->is_active,
                    'is_captain' => $fp->is_captain,
                    'photo' => $fp->player?->user?->photo ? Storage::url($fp->player->user->photo) : null,
                ];
            })->values(),
            'away' => $fixture->fixturePlayers->where('team_id', $fixture->away_team_id)->map(function($fp) {
                return [
                    'id' => $fp->id,
                    'name' => $fp->player?->user?->name ?? $fp->custom_name ?? 'Guest',
                    'is_active' => $fp->is_active,
                    'is_captain' => $fp->is_captain,
                    'photo' => $fp->player?->user?->photo ? Storage::url($fp->player->user->photo) : null,
                ];
            })->values(),
        ];
        
        // Calculate Red Cards for UI indicators
        $redCards = [];
        $rcEvents = $fixture->events->where('event_type', 'RED_CARD');
        foreach ($rcEvents as $ev) {
            if ($ev->team_id) {
                if (!isset($redCards[$ev->team_id])) $redCards[$ev->team_id] = 0;
                $redCards[$ev->team_id]++;
            }
        }

        return response()->json([
            'success' => true,
            'fixture' => [
                'id' => $fixture->id,
                'slug' => $fixture->slug,
                'status' => $fixture->status,
                'home_score' => $fixture->home_score ?? 0,
                'away_score' => $fixture->away_score ?? 0,
                'match_date' => $fixture->match_date?->format('Y-m-d'),
                'match_time' => $fixture->match_time?->format('H:i:s'),
                'formatted_date' => $fixture->match_date?->format('M d, Y'),
                'started_at' => $fixture->started_at,
                'match_duration' => $fixture->match_duration,
                'venue' => $fixture->venue,
                'league_name' => $fixture->league->name,
                'scorer_id' => $fixture->scorer_id,
                'home_team' => [
                    'id' => $fixture->home_team_id,
                    'name' => $fixture->homeTeam?->team?->name ?? 'Home',
                    'logo' => $fixture->homeTeam?->team?->logo ? Storage::url($fixture->homeTeam->team->logo) : null,
                    'red_cards' => $redCards[$fixture->home_team_id] ?? 0,
                ],
                'away_team' => [
                    'id' => $fixture->away_team_id,
                    'name' => $fixture->awayTeam?->team?->name ?? 'Away',
                    'logo' => $fixture->awayTeam?->team?->logo ? Storage::url($fixture->awayTeam->team->logo) : null,
                    'red_cards' => $redCards[$fixture->away_team_id] ?? 0,
                ],
            ],
            'events' => $events,
            'lineups' => $lineups,
        ]);
    }

    public function storeEvent(Request $request, Fixture $fixture)
    {
        // Simple authorization check: User must be scorer or admin or organizer
        if ($request->user()->id !== $fixture->scorer_id && 
            !$request->user()->is_admin && 
            ($fixture->league && $fixture->league->organizer_id !== $request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: User ' . $request->user()->id . ' is not Scorer ' . $fixture->scorer_id], 403);
        }

        $validated = $request->validate([
            'event_type' => 'required|string',
            'minute' => 'required|integer',
            'team_id' => 'nullable|exists:league_teams,id',
            'player_id' => 'nullable|exists:league_players,id',
            'player_name' => 'nullable|string',
            'description' => 'nullable|string',
            'assist_player_id' => 'nullable|exists:league_players,id',
            'assist_player_name' => 'nullable|string',
        ]);

        $event = $fixture->events()->create($validated);

        // Update Score if Goal
        if ($validated['event_type'] === 'GOAL') {
            if ($validated['team_id'] == $fixture->home_team_id) {
                $fixture->increment('home_score');
            } elseif ($validated['team_id'] == $fixture->away_team_id) {
                $fixture->increment('away_score');
            }
        }

        return response()->json([
            'success' => true, 
            'event' => $event,
            'new_scores' => [
                'home' => $fixture->refresh()->home_score,
                'away' => $fixture->away_score
            ]
        ]);
    }

    public function deleteEvent(Request $request, Fixture $fixture, $eventId)
    {
         // Authorization check
        if ($request->user()->id !== $fixture->scorer_id && 
            !$request->user()->is_admin && 
            ($fixture->league && $fixture->league->organizer_id !== $request->user()->id)) {
             return response()->json(['success' => false, 'message' => 'Unauthorized: User ' . $request->user()->id . ' is not Scorer ' . $fixture->scorer_id], 403);
        }

        $event = \App\Models\MatchEvent::findOrFail($eventId);

        if ($event->fixture_id !== $fixture->id) {
            return response()->json(['success' => false, 'message' => 'Event mismatch'], 403);
        }

        // Revert Logic
        if ($event->event_type === 'GOAL') {
             if ($event->team_id == $fixture->home_team_id) {
                 $fixture->decrement('home_score');
             } elseif ($event->team_id == $fixture->away_team_id) {
                 $fixture->decrement('away_score');
             }
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'new_scores' => [
                'home' => $fixture->refresh()->home_score,
            ]
        ]);
    }

    public function substitute(Request $request, Fixture $fixture)
    {
        // Authorization
        if ($request->user()->id !== $fixture->scorer_id && 
            !$request->user()->is_admin && 
            ($fixture->league && $fixture->league->organizer_id !== $request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: User ' . $request->user()->id . ' is not Scorer ' . $fixture->scorer_id], 403);
        }

        $validated = $request->validate([
            'team_id' => 'required|exists:league_teams,id',
            'player_out_id' => 'nullable|exists:league_players,id',
            'player_in_id' => 'nullable|exists:league_players,id',
            'minute' => 'required|integer',
        ]);

        // Record Substitution Event
        $fixture->events()->create([
            'event_type' => 'SUB',
            'minute' => $validated['minute'],
            'team_id' => $validated['team_id'],
            'player_id' => $validated['player_out_id'],
            'related_player_id' => $validated['player_in_id'],
            'description' => 'Substitution',
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
                // If not in bench list (unlikely if data is consistent, but safe to add)
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

    public function finishMatch(Request $request, Fixture $fixture)
    {
         // Authorization
        if ($request->user()->id !== $fixture->scorer_id && 
            !$request->user()->is_admin && 
            ($fixture->league && $fixture->league->organizer_id !== $request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: User ' . $request->user()->id . ' is not Scorer ' . $fixture->scorer_id], 403);
        }

        $fixture->update([
            'status' => 'completed',
        ]);

        return response()->json(['success' => true, 'message' => 'Match Finished']);
    }
}
