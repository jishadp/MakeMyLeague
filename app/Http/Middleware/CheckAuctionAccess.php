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

        // 1. Organizers and Admins can always access auctions for their leagues
        if ($user->isOrganizerForLeague($league->id) || $user->isAdmin()) {
            return $next($request);
        }

        // 2. Team Owners can access auctions (but no CRUD on league)
        $ownsTeamInLeague = $league->leagueTeams()->whereHas('team', function($query) use ($user) {
            $query->whereHas('owners', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'owner');
            });
        })->exists();

        if ($ownsTeamInLeague) {
            return $next($request);
        }

        // 3. Assigned Auctioneers can access auctions for bidding
        $isAuctioneer = $league->leagueTeams()->where('auctioneer_id', $user->id)->exists();

        if ($isAuctioneer) {
            return $next($request);
        }

        // If none of the above, deny access
        abort(403, 'You must be a team owner or assigned auctioneer to access the auction.');
    }
}