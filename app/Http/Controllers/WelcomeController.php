<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Ground;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\Team;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get featured grounds (highest capacity) for the landing page
        $featuredGrounds = Ground::with(['state', 'district', 'localBody'])
            ->where('is_available', true)
            ->orderBy('capacity', 'desc')
            ->take(4)
            ->get();

        // Get featured teams for the landing page
        $featuredTeams = Team::with(['homeGround.district', 'localBody'])
            ->withCount('leagueTeams')
            ->latest('updated_at')
            ->take(4)
            ->get();

        // Get dynamic statistics from database (only approved leagues for public display)
        $stats = [
            'leagues' => League::whereHas('organizers', function($query) {
                $query->where('status', 'approved');
            })->count(),
            'teams' => Team::count(),
            'players' => LeaguePlayer::count(), // Count all league players
            'matches' => Fixture::count(),
        ];

        $liveAuctionLeague = League::withCount('teams')
            ->where('auction_active', true)
            ->orderByDesc('auction_started_at')
            ->first();

        $upcomingLeague = League::withCount('teams')
            ->whereDate('start_date', '>=', now())
            ->orderBy('start_date')
            ->first();

        $nextFixture = Fixture::with([
                'league',
                'homeTeam.team',
                'awayTeam.team',
            ])
            ->whereNotNull('match_date')
            ->orderBy('match_date')
            ->first();

        if (! $nextFixture) {
            $nextFixture = Fixture::with([
                    'league',
                    'homeTeam.team',
                    'awayTeam.team',
                ])
                ->latest('created_at')
                ->first();
        }

        $recentLeagues = League::with(['localBody', 'game'])
            ->withCount('teams')
            ->latest('updated_at')
            ->take(6)
            ->get();

        $opsMetrics = [
            'leaguesThisMonth' => League::where('created_at', '>=', now()->subDays(30))->count(),
            'fixturesScheduled' => Fixture::whereDate('match_date', '>=', now())->count(),
            'groundsAvailable' => Ground::where('is_available', true)->count(),
            'teamsThisWeek' => Team::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('welcome', compact(
            'featuredGrounds',
            'featuredTeams',
            'stats',
            'liveAuctionLeague',
            'upcomingLeague',
            'nextFixture',
            'recentLeagues',
            'opsMetrics'
        ));
    }
}
