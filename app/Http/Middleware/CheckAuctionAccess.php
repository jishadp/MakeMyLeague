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
            // Check if auction access is granted (admins can always access)
            if ($league->hasAuctionAccess() || $user->isAdmin()) {
                return $next($request);
            } else {
                abort(403, 'Auction access has not been granted for this league. Please contact the admin to request access.');
            }
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
            // Check if auction access is granted for the league
            if (!$league->hasAuctionAccess()) {
                abort(403, 'Auction access has not been granted for this league. Please contact the league organizer to request access.');
            }
            
            // Check if any player is currently being auctioned (meaning auction is live)
            $auctioningPlayer = \App\Models\LeaguePlayer::where('league_id', $league->id)
                ->where('status', 'auctioning')
                ->exists();
                
            if ($auctioningPlayer || $league->isAuctionActive()) {
                return $next($request);
            } else {
                abort(403, 'Auction is not currently live. Only live auctions are accessible to team owners.');
            }
        }

        // If user doesn't own a team in this league, deny access
        abort(403, 'You must own a team in this league to access the auction.');
    }
}
