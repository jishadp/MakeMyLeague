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
        
        if (!$league) {
            abort(404, 'League not found');
        }

        // Check if user is an organizer for this league
        if (!$user->isOrganizerForLeague($league->id)) {
            abort(403, 'You are not authorized to perform this action. Only league organizers can access this page.');
        }

        return $next($request);
    }
}