<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fixture;

class LiveMatchController extends Controller
{
    public function show(Fixture $fixture)
    {
        // Eager load everything needed for the public page EXCEPT events (fetched manually below)
        $fixture->load([
            'homeTeam', 
            'awayTeam', 
            'fixturePlayers.player.user', // For lineups
            'scorer'
        ]);

        // Fetch paginated events
        $events = $fixture->events()
            ->with(['player.user', 'assistPlayer.user', 'relatedPlayer.user', 'team'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Fetch summary data for scoreboard (all goals and red cards)
        $goals = $fixture->events()
            ->where('event_type', 'GOAL')
            ->with(['player.user', 'team'])
            ->orderBy('minute')
            ->get();
            
        $redCards = $fixture->events()
            ->where('event_type', 'RED_CARD')
            ->get()
            ->groupBy('team_id');

        return view('matches.live', compact('fixture', 'events', 'goals', 'redCards'));
    }
}
