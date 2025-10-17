<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTeamOwner
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

        // Check if user owns a team in this league
        $ownsTeamInLeague = $league->leagueTeams()->whereHas('team', function($query) use ($user) {
            $query->whereHas('owners', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', 'owner');
            });
        })->exists();

        // Also allow organizers and admins
        if (!$ownsTeamInLeague && !$user->isOrganizerForLeague($league->id) && !$user->isAdmin()) {
            abort(403, 'You must be a team owner to perform this action.');
        }

        return $next($request);
    }
}
