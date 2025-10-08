<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuctioneerController extends Controller
{
    /**
     * Assign an auctioneer to a league team.
     */
    public function assign(Request $request, League $league, LeagueTeam $leagueTeam)
    {
        // Check if the current user is authorized to assign auctioneers for this team
        if (!$this->canAssignAuctioneer($leagueTeam)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to assign auctioneers for this team.'
            ], 403);
        }

        $request->validate([
            'auctioneer_id' => 'required|exists:users,id'
        ]);

        // Additional validation: Check if the auctioneer is the same as the current user
        if ($request->auctioneer_id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot assign yourself as the auctioneer.'
            ], 422);
        }

        $auctioneer = User::findOrFail($request->auctioneer_id);

        // Check if the auction has already started
        if ($league->isAuctionActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign auctioneers after the auction has started.'
            ], 422);
        }

        // Check if the auctioneer can be assigned to this league
        if (!$auctioneer->canBeAuctioneerForLeague($league->id, $leagueTeam->id)) {
            return response()->json([
                'success' => false,
                'message' => 'This user is already assigned as an auctioneer for another team in this league.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update the league team with the new auctioneer
            $leagueTeam->update(['auctioneer_id' => $auctioneer->id]);

            // Send notification to the assigned auctioneer
            $this->sendAuctioneerAssignmentNotification($auctioneer, $league, $leagueTeam);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Auctioneer assigned successfully.',
                'auctioneer' => [
                    'id' => $auctioneer->id,
                    'name' => $auctioneer->name,
                    'email' => $auctioneer->email
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign auctioneer. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove an auctioneer from a league team.
     */
    public function remove(Request $request, League $league, LeagueTeam $leagueTeam)
    {
        // Check if the current user is authorized to remove auctioneers for this team
        if (!$this->canAssignAuctioneer($leagueTeam)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to remove auctioneers for this team.'
            ], 403);
        }

        // Check if the auction has already started
        if ($league->isAuctionActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove auctioneers after the auction has started.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $auctioneer = $leagueTeam->auctioneer;
            $leagueTeam->update(['auctioneer_id' => null]);

            // Send notification to the removed auctioneer
            if ($auctioneer) {
                $this->sendAuctioneerRemovalNotification($auctioneer, $league, $leagueTeam);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Auctioneer removed successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove auctioneer. Please try again.'
            ], 500);
        }
    }

    /**
     * Get available auctioneers for a league.
     */
    public function getAvailableAuctioneers(League $league)
    {
        // Check if the current user is authorized to view auctioneer information
        if (!$this->canViewAuctioneerInfo($league)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view auctioneer information for this league.'
            ], 403);
        }

        $availableAuctioneers = $league->getAvailableAuctioneers();

        return response()->json([
            'success' => true,
            'auctioneers' => $availableAuctioneers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->mobile ? ($user->country_code . ' ' . $user->mobile) : null
                ];
            })
        ]);
    }

    /**
     * Search users for auctioneer assignment.
     */
    public function searchUsers(Request $request, League $league)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2'
            ]);

            // Check if the current user is authorized to search for auctioneers
            if (!$this->canViewAuctioneerInfo($league)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to search for auctioneers.'
                ], 403);
            }

            $query = $request->input('query');
            $assignedAuctioneerIds = $league->leagueTeams()
                ->whereNotNull('auctioneer_id')
                ->pluck('auctioneer_id')
                ->toArray();

            $users = User::where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->whereNotIn('id', $assignedAuctioneerIds)
                ->where('id', '!=', 1) // Exclude admin user
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->mobile ? ($user->country_code . ' ' . $user->mobile) : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Auctioneer search error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'league_id' => $league->id ?? 'not found'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for users.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if the current user can assign auctioneers for a league team.
     */
    private function canAssignAuctioneer(LeagueTeam $leagueTeam): bool
    {
        $user = Auth::user();
        
        // Check if user is the team owner (primary owner)
        if ($user->isOwnerOfTeam($leagueTeam->team_id)) {
            return true;
        }

        // Check if user is a co-owner of the team
        if ($user->isCoOwnerOfTeam($leagueTeam->team_id)) {
            return true;
        }

        // Check if user is an organizer of the league
        if ($user->isOrganizerForLeague($leagueTeam->league_id)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current user can view auctioneer information for a league.
     */
    private function canViewAuctioneerInfo(League $league): bool
    {
        $user = Auth::user();
        
        // Check if user is an organizer of the league
        if ($user->isOrganizerForLeague($league->id)) {
            return true;
        }

        // Check if user owns any teams in this league
        $userTeams = $league->leagueTeams()->whereHas('team', function ($query) use ($user) {
            $query->whereHas('owners', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })->exists();

        return $userTeams;
    }

    /**
     * Send notification to auctioneer when assigned.
     */
    private function sendAuctioneerAssignmentNotification($auctioneer, $league, $leagueTeam)
    {
        \App\Models\Notification::create([
            'user_id' => $auctioneer->id,
            'type' => 'auctioneer_assigned',
            'title' => 'Auctioneer Assignment',
            'message' => "You have been assigned as the auctioneer for team '{$leagueTeam->team->name}' in the '{$league->name}' league.",
            'data' => [
                'league_id' => $league->id,
                'league_name' => $league->name,
                'team_id' => $leagueTeam->team_id,
                'team_name' => $leagueTeam->team->name,
                'league_team_id' => $leagueTeam->id,
            ]
        ]);
    }

    /**
     * Send notification to auctioneer when removed.
     */
    private function sendAuctioneerRemovalNotification($auctioneer, $league, $leagueTeam)
    {
        \App\Models\Notification::create([
            'user_id' => $auctioneer->id,
            'type' => 'auctioneer_removed',
            'title' => 'Auctioneer Assignment Removed',
            'message' => "Your auctioneer assignment for team '{$leagueTeam->team->name}' in the '{$league->name}' league has been removed.",
            'data' => [
                'league_id' => $league->id,
                'league_name' => $league->name,
                'team_id' => $leagueTeam->team_id,
                'team_name' => $leagueTeam->team->name,
                'league_team_id' => $leagueTeam->id,
            ]
        ]);
    }
}