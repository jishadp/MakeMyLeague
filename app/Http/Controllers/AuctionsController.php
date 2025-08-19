<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuctionsController extends Controller
{
    /**
     * Display a listing of the auctions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get active auctions (static for now)
        $activeAuctions = [
            [
                'id' => 1,
                'league_name' => 'Premier Cricket League 2025',
                'status' => 'active',
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-20',
                'description' => 'The flagship cricket auction for the 2025 season featuring top players from around the world.',
                'teams_count' => 8,
                'players_count' => 96,
                'image' => 'https://picsum.photos/800/400?random=1',
                'total_budget' => '$5,000,000',
            ],
            [
                'id' => 2,
                'league_name' => 'Regional T20 Championship',
                'status' => 'upcoming',
                'start_date' => '2025-09-05',
                'end_date' => '2025-09-15',
                'description' => 'A regional tournament featuring the best local talent, with teams competing for top regional players.',
                'teams_count' => 6,
                'players_count' => 72,
                'image' => 'https://picsum.photos/800/400?random=2',
                'total_budget' => '$2,500,000',
            ],
            [
                'id' => 3,
                'league_name' => 'International Cricket Masters',
                'status' => 'completed',
                'start_date' => '2025-07-10',
                'end_date' => '2025-07-25',
                'description' => 'A special auction featuring veteran cricket legends coming out of retirement for one final season.',
                'teams_count' => 4,
                'players_count' => 48,
                'image' => 'https://picsum.photos/800/400?random=3',
                'total_budget' => '$3,200,000',
            ],
        ];

        // Get featured players
        $featuredPlayers = User::with(['position', 'localBody'])
            ->players()
            ->take(4)
            ->get();

        return view('auctions.index', compact('activeAuctions', 'featuredPlayers'));
    }

    /**
     * Display the specified auction.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Mock data for a single auction
        $auction = [
            'id' => $id,
            'league_name' => 'Premier Cricket League 2025',
            'status' => 'active',
            'start_date' => '2025-08-01',
            'end_date' => '2025-08-20',
            'description' => 'The flagship cricket auction for the 2025 season featuring top players from around the world. Teams will bid for players in a competitive environment to build their dream squads for the upcoming season.',
            'teams_count' => 8,
            'players_count' => 96,
            'image' => 'https://picsum.photos/1200/600?random=1',
            'total_budget' => '$5,000,000',
            'rules' => [
                'Each team has a maximum budget of $625,000',
                'Teams must acquire a minimum of 12 players',
                'Maximum of 4 international players per team',
                'Player bidding starts at a base price determined by their experience level',
                'Auction follows a round-robin format with each team getting equal opportunities to bid'
            ],
        ];

        // Mock data for teams in this auction
        $teams = [
            [
                'id' => 1,
                'name' => 'Royal Challengers',
                'logo' => 'https://picsum.photos/100/100?random=1',
                'players_acquired' => 5,
                'remaining_budget' => '$425,000',
            ],
            [
                'id' => 2,
                'name' => 'Super Kings',
                'logo' => 'https://picsum.photos/100/100?random=2',
                'players_acquired' => 7,
                'remaining_budget' => '$350,000',
            ],
            [
                'id' => 3,
                'name' => 'Panthers',
                'logo' => 'https://picsum.photos/100/100?random=3',
                'players_acquired' => 6,
                'remaining_budget' => '$375,000',
            ],
            [
                'id' => 4,
                'name' => 'Titans',
                'logo' => 'https://picsum.photos/100/100?random=4',
                'players_acquired' => 8,
                'remaining_budget' => '$310,000',
            ],
        ];

        // Mock data for recent player acquisitions
        $recentAcquisitions = [
            [
                'player_name' => 'Michael Johnson',
                'player_role' => 'Batsman',
                'team_name' => 'Royal Challengers',
                'amount' => '$75,000',
                'time' => '2 hours ago',
            ],
            [
                'player_name' => 'David Smith',
                'player_role' => 'Bowler',
                'team_name' => 'Super Kings',
                'amount' => '$65,000',
                'time' => '4 hours ago',
            ],
            [
                'player_name' => 'Chris Williams',
                'player_role' => 'All-rounder',
                'team_name' => 'Panthers',
                'amount' => '$90,000',
                'time' => '8 hours ago',
            ],
            [
                'player_name' => 'Jason Brown',
                'player_role' => 'Wicket-keeper',
                'team_name' => 'Titans',
                'amount' => '$85,000',
                'time' => '10 hours ago',
            ],
            [
                'player_name' => 'Steven Davis',
                'player_role' => 'Bowler',
                'team_name' => 'Royal Challengers',
                'amount' => '$60,000',
                'time' => '12 hours ago',
            ],
        ];

        // Get featured players
        $availablePlayers = User::with(['position', 'localBody'])
            ->players()
            ->take(8)
            ->get();

        return view('auctions.show', compact('auction', 'teams', 'recentAcquisitions', 'availablePlayers'));
    }
}
