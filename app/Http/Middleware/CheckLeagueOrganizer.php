<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLeagueOrganizer
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
        
        // Get route name for debugging
        $routeName = $request->route()?->getName();
        
        // If this is the index, create, or store route (no league parameter), allow any authenticated user
        if (!$league && in_array($routeName, ['leagues.index', 'leagues.create', 'leagues.store'])) {
            return $next($request);
        }
        
        if (!$league) {
            abort(404, 'League not found');
        }

        // Check if user is an organizer for this league or is an admin
        if (!$user->isOrganizerForLeague($league->id) && !$user->isAdmin()) {
            abort(403, 'You are not authorized to perform this action. Only league organizers or admins can access this page.');
        }

        return $next($request);
    }
}