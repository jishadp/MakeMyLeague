<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\TeamAuctioneer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AuctioneerManagementController extends Controller
{
    /**
     * Display a listing of all leagues with auctioneer information.
     */
    public function index(): View
    {
        try {
            $leagues = League::with([
                'game',
                'leagueTeams' => function($query) {
                    $query->with(['team', 'teamAuctioneer.auctioneer']);
                }
            ])
            ->withCount('leagueTeams')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

            // Get statistics
            $stats = [
                'total_leagues' => League::count(),
                'total_teams' => LeagueTeam::count(),
                'total_assignments' => TeamAuctioneer::where('status', 'active')->count(),
                'leagues_with_auctions' => League::where('auction_active', true)->count(),
            ];

            return view('admin.auctioneers.index', [
                'leagues' => $leagues,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Auctioneer Management Index Error: ' . $e->getMessage());
            
            return view('admin.auctioneers.index', [
                'leagues' => collect(),
                'stats' => [
                    'total_leagues' => 0,
                    'total_teams' => 0,
                    'total_assignments' => 0,
                    'leagues_with_auctions' => 0,
                ]
            ])->with('error', 'An error occurred loading the auctioneer management page.');
        }
    }

    /**
     * Display the auctioneer details for a specific league.
     */
    public function show(League $league): View
    {
        try {
            $leagueTeams = $league->leagueTeams()
                ->with([
                    'team',
                    'teamAuctioneer' => function($query) {
                        $query->where('status', 'active')->with('auctioneer');
                    },
                    'teamAuctioneers' => function($query) {
                        $query->with('auctioneer')->orderBy('created_at', 'desc');
                    }
                ])
                ->get();

            // Get unassigned teams
            $unassignedTeams = $leagueTeams->filter(function($leagueTeam) {
                return !$leagueTeam->teamAuctioneer;
            });

            // Get assigned teams
            $assignedTeams = $leagueTeams->filter(function($leagueTeam) {
                return $leagueTeam->teamAuctioneer;
            });

            // Get available users (not assigned in this league)
            $assignedAuctioneerIds = TeamAuctioneer::where('league_id', $league->id)
                ->where('status', 'active')
                ->pluck('auctioneer_id')
                ->toArray();

            $availableUsers = User::whereNotIn('id', $assignedAuctioneerIds)
                ->where('id', '!=', 1) // Exclude admin
                ->orderBy('name')
                ->get();

            return view('admin.auctioneers.show', [
                'league' => $league,
                'leagueTeams' => $leagueTeams,
                'unassignedTeams' => $unassignedTeams,
                'assignedTeams' => $assignedTeams,
                'availableUsers' => $availableUsers,
            ]);
        } catch (\Exception $e) {
            \Log::error('Auctioneer Management Show Error: ' . $e->getMessage());
            
            return redirect()->route('admin.auctioneers.index')
                ->with('error', 'An error occurred loading the league details.');
        }
    }

    /**
     * Assign an auctioneer to a team (Admin method).
     */
    public function assign(Request $request, League $league, LeagueTeam $leagueTeam): JsonResponse
    {
        $request->validate([
            'auctioneer_id' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $auctioneerId = $request->auctioneer_id;

            // Check if user is already an auctioneer in this league
            if (TeamAuctioneer::isAuctioneerInLeague($league->id, $auctioneerId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user is already assigned as an auctioneer for another team in this league.'
                ], 422);
            }

            // Remove existing assignment if any
            TeamAuctioneer::removeAuctioneer($leagueTeam->id);

            // Assign the new auctioneer
            $teamAuctioneer = TeamAuctioneer::assignAuctioneer(
                $league->id,
                $leagueTeam->team_id,
                $leagueTeam->id,
                $auctioneerId,
                'Assigned by admin'
            );

            // Also update the legacy column for backward compatibility
            $leagueTeam->update(['auctioneer_id' => $auctioneerId]);

            // Send notification
            \App\Models\Notification::create([
                'user_id' => $auctioneerId,
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Auctioneer assigned successfully.',
                'auctioneer' => $teamAuctioneer->auctioneer
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Auctioneer Assignment Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove an auctioneer from a team (Admin method).
     */
    public function remove(League $league, LeagueTeam $leagueTeam): JsonResponse
    {
        try {
            DB::beginTransaction();

            $teamAuctioneerAssignment = $leagueTeam->teamAuctioneer;
            
            if (!$teamAuctioneerAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No auctioneer assigned to this team.'
                ], 404);
            }

            $auctioneer = $teamAuctioneerAssignment->auctioneer;

            // Remove the auctioneer assignment
            TeamAuctioneer::removeAuctioneer($leagueTeam->id);

            // Also update the legacy column for backward compatibility
            $leagueTeam->update(['auctioneer_id' => null]);

            // Send notification
            if ($auctioneer) {
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Auctioneer removed successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Auctioneer Removal Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove auctioneer. Please try again.'
            ], 500);
        }
    }

    /**
     * Get auctioneer statistics for a league.
     */
    public function stats(League $league): JsonResponse
    {
        try {
            $totalTeams = $league->leagueTeams()->count();
            $assignedTeams = TeamAuctioneer::where('league_id', $league->id)
                ->where('status', 'active')
                ->count();
            
            $unassignedTeams = $totalTeams - $assignedTeams;
            $completionPercentage = $totalTeams > 0 ? round(($assignedTeams / $totalTeams) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_teams' => $totalTeams,
                    'assigned_teams' => $assignedTeams,
                    'unassigned_teams' => $unassignedTeams,
                    'completion_percentage' => $completionPercentage,
                    'auction_active' => $league->isAuctionActive(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Auctioneer Stats Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics.'
            ], 500);
        }
    }
}
