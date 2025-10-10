<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use App\Models\League;
use App\Models\Auction;
use App\Models\LeaguePlayer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request): View
    {
        // Get date range from request or default to last 30 days
        $dateRange = $request->get('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        // Player Registration Analytics
        $playerStats = $this->getPlayerRegistrationStats($startDate, $endDate);
        
        // Teams Created Analytics
        $teamStats = $this->getTeamCreationStats($startDate, $endDate);
        
        // Leagues Created Analytics
        $leagueStats = $this->getLeagueCreationStats($startDate, $endDate);
        
        // Auction Completed Analytics
        $auctionStats = $this->getAuctionCompletionStats($startDate, $endDate);

        // Activity Logs
        $activityLogs = $this->getActivityLogs($startDate, $endDate);

        return view('admin.analytics.index', compact(
            'playerStats',
            'teamStats', 
            'leagueStats',
            'auctionStats',
            'activityLogs',
            'dateRange'
        ));
    }

    /**
     * Get player registration statistics.
     */
    private function getPlayerRegistrationStats($startDate, $endDate)
    {
        // Count all users (everyone is a player in the system)
        $totalPlayers = User::count();
        
        $newPlayers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        $dailyRegistrations = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $positionBreakdown = User::whereBetween('created_at', [$startDate, $endDate])
            ->with(['position', 'roles'])
            ->get()
            ->groupBy(function($user) {
                // Group by role name or position name
                if ($user->hasRole('Admin')) return 'Admin';
                if ($user->hasRole('Organiser')) return 'Organiser';
                if ($user->hasRole('Player')) return 'Player';
                return $user->position->name ?? 'No Position';
            })
            ->map(function ($group) {
                return $group->count();
            });

        return [
            'total' => $totalPlayers,
            'new' => $newPlayers,
            'daily' => $dailyRegistrations,
            'by_position' => $positionBreakdown
        ];
    }

    /**
     * Get team creation statistics.
     */
    private function getTeamCreationStats($startDate, $endDate)
    {
        $totalTeams = Team::count();
        $newTeams = Team::whereBetween('created_at', [$startDate, $endDate])->count();

        $dailyCreations = Team::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total' => $totalTeams,
            'new' => $newTeams,
            'daily' => $dailyCreations
        ];
    }

    /**
     * Get league creation statistics.
     */
    private function getLeagueCreationStats($startDate, $endDate)
    {
        $totalLeagues = League::count();
        $newLeagues = League::whereBetween('created_at', [$startDate, $endDate])->count();

        $dailyCreations = League::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $statusBreakdown = League::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return [
            'total' => $totalLeagues,
            'new' => $newLeagues,
            'daily' => $dailyCreations,
            'by_status' => $statusBreakdown
        ];
    }

    /**
     * Get auction completion statistics.
     */
    private function getAuctionCompletionStats($startDate, $endDate)
    {
        $totalAuctions = Auction::count();
        $completedAuctions = Auction::where('status', 'won')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $dailyCompletions = Auction::where('status', 'won')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $playersSold = LeaguePlayer::whereHas('auctionBids', function($query) use ($startDate, $endDate) {
            $query->where('status', 'won')
                  ->whereBetween('updated_at', [$startDate, $endDate]);
        })->count();

        return [
            'total' => $totalAuctions,
            'completed' => $completedAuctions,
            'daily' => $dailyCompletions,
            'players_sold' => $playersSold
        ];
    }

    /**
     * Get activity logs for the date range.
     */
    private function getActivityLogs($startDate, $endDate)
    {
        $logs = collect();

        // User registrations (all users)
        $playerLogs = User::whereBetween('created_at', [$startDate, $endDate])
            ->with('roles')
            ->select('name', 'created_at')
            ->get()
            ->map(function ($user) {
                $userType = $user->hasRole('Admin') ? 'admin' : ($user->hasRole('Organiser') ? 'organiser' : 'player');
                return [
                    'type' => 'player_registration',
                    'description' => "New {$userType} registered: {$user->name}",
                    'timestamp' => $user->created_at,
                    'icon' => 'user-plus',
                    'color' => 'blue'
                ];
            });

        // Team creations
        $teamLogs = Team::whereBetween('created_at', [$startDate, $endDate])
            ->select('name', 'created_at')
            ->get()
            ->map(function ($team) {
                return [
                    'type' => 'team_creation',
                    'description' => "New team created: {$team->name}",
                    'timestamp' => $team->created_at,
                    'icon' => 'users',
                    'color' => 'green'
                ];
            });

        // League creations
        $leagueLogs = League::whereBetween('created_at', [$startDate, $endDate])
            ->select('name', 'created_at')
            ->get()
            ->map(function ($league) {
                return [
                    'type' => 'league_creation',
                    'description' => "New league created: {$league->name}",
                    'timestamp' => $league->created_at,
                    'icon' => 'trophy',
                    'color' => 'purple'
                ];
            });

        // Auction completions
        $auctionLogs = Auction::where('status', 'won')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with(['leaguePlayer.league', 'leagueTeam.team'])
            ->select('id', 'league_player_id', 'league_team_id', 'updated_at')
            ->get()
            ->map(function ($auction) {
                $leagueName = $auction->leaguePlayer->league->name ?? 'Unknown League';
                $teamName = $auction->leagueTeam->team->name ?? 'Unknown Team';
                return [
                    'type' => 'auction_completion',
                    'description' => "Player sold to {$teamName} in league: {$leagueName}",
                    'timestamp' => $auction->updated_at,
                    'icon' => 'gavel',
                    'color' => 'orange'
                ];
            });

        $logs = $logs->merge($playerLogs)
                    ->merge($teamLogs)
                    ->merge($leagueLogs)
                    ->merge($auctionLogs);

        // Sort by timestamp and paginate manually (10 per page)
        $sortedLogs = $logs->sortByDesc('timestamp');

        // Get current page from request, default to 1
        $currentPage = request('page', 1);
        $perPage = 10;

        // Calculate offset
        $offset = ($currentPage - 1) * $perPage;

        // Get items for current page
        $items = $sortedLogs->slice($offset, $perPage);

        // Create a LengthAwarePaginator manually
        $total = $sortedLogs->count();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return $paginator;
    }

    /**
     * Get detailed player registration data for export.
     */
    public function playerRegistrations(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        $players = User::whereBetween('created_at', [$startDate, $endDate])
            ->with(['position', 'localBody.district.state', 'roles'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.analytics.player-registrations', compact('players', 'dateRange'));
    }

    /**
     * Get detailed team creation data for export.
     */
    public function teamCreations(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        $teams = Team::whereBetween('created_at', [$startDate, $endDate])
            ->with(['owner'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.analytics.team-creations', compact('teams', 'dateRange'));
    }

    /**
     * Get detailed league creation data for export.
     */
    public function leagueCreations(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        $leagues = League::whereBetween('created_at', [$startDate, $endDate])
            ->with(['game', 'approvedOrganizers'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.analytics.league-creations', compact('leagues', 'dateRange'));
    }

    /**
     * Get detailed auction completion data for export.
     */
    public function auctionCompletions(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        $auctions = Auction::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with(['league'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.analytics.auction-completions', compact('auctions', 'dateRange'));
    }
}
