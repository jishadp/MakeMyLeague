<?php

namespace App\Http\Middleware;

use App\Models\League;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAuctionRequirements
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the league from the route parameter
        $league = $request->route('league');
        
        // Ensure we have a valid League model instance
        if (!$league || !($league instanceof League)) {
            abort(404, 'League not found');
        }
        
        // Admin can always bypass these checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if teams requirement is met
        $teamsCount = $league->leagueTeams()->count();
        if ($teamsCount < $league->max_teams) {
            $remaining = $league->max_teams - $teamsCount;
            $message = "Not enough teams registered. This league requires {$league->max_teams} teams, but only {$teamsCount} are registered. {$remaining} more team(s) needed.";
            
            // For organizers, show a more actionable message
            if ($user->isOrganizerForLeague($league->id)) {
                $message .= " Please complete team registrations before starting the auction.";
            }
            
            return redirect()->route('leagues.show', $league->slug)->with('error', $message);
        }

        // Check if players requirement is met
        $totalPlayersRequired = $league->max_teams * $league->max_team_players;
        
        // Count all eligible players (excluding 'pending' and 'skip' status)
        // Also include 'auctioning' status as these players are part of the auction
        $playersCount = $league->leaguePlayers()
            ->whereIn('status', ['available', 'sold', 'unsold', 'auctioning'])
            ->count();
        
        // Debug: Log the actual counts
        \Log::info('Auction Requirements Check', [
            'league' => $league->name,
            'teams_required' => $league->max_teams,
            'teams_registered' => $teamsCount,
            'max_team_players' => $league->max_team_players,
            'total_players_required' => $totalPlayersRequired,
            'players_count' => $playersCount,
            'user' => $user->name,
            'is_admin' => $user->isAdmin()
        ]);
        
        if ($playersCount < $totalPlayersRequired) {
            $remaining = $totalPlayersRequired - $playersCount;
            $message = "Not enough players registered. This league requires {$totalPlayersRequired} players ({$league->max_teams} teams × {$league->max_team_players} players), but only {$playersCount} are registered. {$remaining} more player(s) needed.";
            
            // For organizers, show a more actionable message
            if ($user->isOrganizerForLeague($league->id)) {
                $message .= " Please complete player registrations before starting the auction.";
            }
            
            return redirect()->route('leagues.show', $league->slug)->with('error', $message);
        }

        return $next($request);
    }
}
