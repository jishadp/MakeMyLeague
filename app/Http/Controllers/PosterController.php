<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueTeam;

class PosterController extends Controller
{
    public function listAll()
    {
        $leagues = League::with(['leagueTeams.team', 'leagueTeams.players'])
            ->whereHas('leagueTeams')
            ->get();
        
        return view('posters.list-all', compact('leagues'));
    }

    public function show(League $league, LeagueTeam $leagueTeam)
    {
        $leagueTeam->load([
            'team.owners',
            'team.homeGround',
            'players.user.position',
        ]);
        $owner = $leagueTeam->team->owners->where('role', 'owner')->first();
        $players = $leagueTeam->players->sortBy(fn ($player) => $player->user->name);
        
        return view('posters.show', compact('league', 'leagueTeam', 'owner', 'players'));
    }

}
