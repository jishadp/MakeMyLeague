<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAuctionAccess
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
        
        if (!$league) {
            abort(404, 'League not found');
        }

        // Check if user is an organizer for this league or is an admin
        if ($user->isOrganizerForLeague($league->id) || $user->isAdmin()) {
            // Organizers and admins can always access auctions for their leagues
            return $next($request);
        }

        // For team owners: Check if they own a team in this league 
        $ownsTeamInLeague = $league->leagueTeams()->whereHas('team', function($query) use ($user) {
            $query->whereHas('owners', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'owner');
            });
        })->exists();

        // Also check if the user is an assigned auctioneer
        $isAuctioneer = $league->leagueTeams()->where('auctioneer_id', $user->id)->exists();

        if ($ownsTeamInLeague || $isAuctioneer) {
            // Team owners and auctioneers can access auctions
            return $next($request);
        }

        // If user doesn't own a team in this league, deny access
        abort(403, 'You must own a team in this league to access the auction.');
    }
}
