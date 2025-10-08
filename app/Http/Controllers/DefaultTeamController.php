<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DefaultTeamController extends Controller
{
    /**
     * Set the default team for bidding.
     */
    public function setDefault(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ]);

        $user = Auth::user();
        $team = Team::findOrFail($request->team_id);

        // Check if the user owns this team
        if (!$user->hasOwnershipOfTeam($team->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You can only set teams you own as default.'
            ], 403);
        }

        // Set the default team
        if ($user->setDefaultTeam($team->id)) {
            return response()->json([
                'success' => true,
                'message' => 'Default team updated successfully.',
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'slug' => $team->slug
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update default team.'
        ], 500);
    }

    /**
     * Remove the default team (set to null).
     */
    public function removeDefault(Request $request)
    {
        $user = Auth::user();

        if ($user->setDefaultTeam(null)) {
            return response()->json([
                'success' => true,
                'message' => 'Default team removed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove default team.'
        ], 500);
    }

    /**
     * Get user's owned teams for default team selection.
     */
    public function getOwnedTeams(Request $request)
    {
        $user = Auth::user();
        
        $teams = $user->ownedTeams()
            ->with(['leagueTeams.league'])
            ->get()
            ->map(function ($team) {
                $leagues = $team->leagueTeams->map(function ($leagueTeam) {
                    return [
                        'id' => $leagueTeam->league->id,
                        'name' => $leagueTeam->league->name,
                        'slug' => $leagueTeam->league->slug
                    ];
                });

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'slug' => $team->slug,
                    'is_default' => Auth::user()->default_team_id === $team->id,
                    'leagues' => $leagues
                ];
            });

        return response()->json([
            'success' => true,
            'teams' => $teams
        ]);
    }
}