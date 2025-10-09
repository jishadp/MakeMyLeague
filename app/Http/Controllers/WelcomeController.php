<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Models\LeaguePlayer;
use App\Models\User;
use App\Models\Ground;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get featured grounds (highest capacity) for the landing page
        $featuredGrounds = Ground::with(['state', 'district', 'localBody'])
            ->where('is_available', true)
            ->orderBy('capacity', 'desc')
            ->take(3)
            ->get();

        // Get featured teams for the landing page
        $featuredTeams = Team::with(['homeGround', 'localBody'])
            ->take(3)
            ->get();

        // Get dynamic statistics from database
        $stats = [
            'leagues' => League::count(),
            'teams' => Team::count(),
            'players' => LeaguePlayer::count(), // Count all league players
            'matches' => 10000 // This could be calculated from fixtures if you have that data
        ];

        return view('welcome', compact('featuredGrounds', 'featuredTeams', 'stats'));
    }
}
