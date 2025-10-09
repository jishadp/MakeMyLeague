<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AuctionPanelController extends Controller
{
    /**
     * Display the auction panel management dashboard.
     */
    public function index(): View
    {
        try {
            $pendingRequests = League::getPendingAuctionAccessRequests();
            $grantedAccess = League::getLeaguesWithAuctionAccess();
            
            $totalLeagues = League::count();
            
            // Get statistics
            $stats = [
                'pending_requests' => $pendingRequests->count(),
                'granted_access' => $grantedAccess->count(),
                'total_leagues' => $totalLeagues,
                'auction_enabled_percentage' => $totalLeagues > 0 ? round(($grantedAccess->count() / $totalLeagues) * 100, 2) : 0
            ];

            return view('admin.auction-panel.index', [
                'pendingRequests' => $pendingRequests,
                'grantedAccess' => $grantedAccess,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Auction Panel Error: ' . $e->getMessage());
            
            // Provide default values in case of error
            $pendingRequests = collect([]);
            $grantedAccess = collect([]);
            $stats = [
                'pending_requests' => 0,
                'granted_access' => 0,
                'total_leagues' => 0,
                'auction_enabled_percentage' => 0
            ];
            
            return view('admin.auction-panel.index', [
                'pendingRequests' => $pendingRequests,
                'grantedAccess' => $grantedAccess,
                'stats' => $stats
            ])->with('error', 'An error occurred loading the auction panel: ' . $e->getMessage());
        }
    }

    /**
     * Grant auction access to a league.
     */
    public function grantAccess(Request $request, League $league): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $league->grantAuctionAccess($request->input('notes'));

        return response()->json([
            'success' => true,
            'message' => 'Auction access granted successfully for ' . $league->name
        ]);
    }

    /**
     * Revoke auction access from a league.
     */
    public function revokeAccess(Request $request, League $league): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $league->revokeAuctionAccess($request->input('notes'));

        return response()->json([
            'success' => true,
            'message' => 'Auction access revoked from ' . $league->name
        ]);
    }

    /**
     * Bulk grant auction access to multiple leagues.
     */
    public function bulkGrantAccess(Request $request): JsonResponse
    {
        $request->validate([
            'league_ids' => 'required|array',
            'league_ids.*' => 'exists:leagues,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $leagueIds = $request->input('league_ids');
        $notes = $request->input('notes');
        
        $updatedCount = League::whereIn('id', $leagueIds)
            ->update([
                'auction_access_granted' => true,
                'auction_access_notes' => $notes
            ]);

        return response()->json([
            'success' => true,
            'message' => "Auction access granted to {$updatedCount} leagues"
        ]);
    }

    /**
     * Bulk revoke auction access from multiple leagues.
     */
    public function bulkRevokeAccess(Request $request): JsonResponse
    {
        $request->validate([
            'league_ids' => 'required|array',
            'league_ids.*' => 'exists:leagues,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $leagueIds = $request->input('league_ids');
        $notes = $request->input('notes');
        
        $updatedCount = League::whereIn('id', $leagueIds)
            ->update([
                'auction_access_granted' => false,
                'auction_access_notes' => $notes,
                'auction_access_requested_at' => null
            ]);

        return response()->json([
            'success' => true,
            'message' => "Auction access revoked from {$updatedCount} leagues"
        ]);
    }

    /**
     * Get league details for modal.
     */
    public function getLeagueDetails(League $league): JsonResponse
    {
        $league->load(['game', 'approvedOrganizers', 'leagueTeams.team', 'leaguePlayers']);
        
        $details = [
            'id' => $league->id,
            'name' => $league->name,
            'game' => $league->game->name ?? 'N/A',
            'status' => $league->status,
            'organizers' => $league->approvedOrganizers->pluck('name')->join(', '),
            'teams_count' => $league->leagueTeams->count(),
            'players_count' => $league->leaguePlayers->count(),
            'max_teams' => $league->max_teams,
            'max_team_players' => $league->max_team_players,
            'team_wallet_limit' => $league->team_wallet_limit,
            'auction_access_granted' => $league->auction_access_granted,
            'auction_access_requested_at' => $league->auction_access_requested_at,
            'auction_access_notes' => $league->auction_access_notes,
            'created_at' => $league->created_at->format('M d, Y'),
            'start_date' => $league->start_date->format('M d, Y'),
            'end_date' => $league->end_date->format('M d, Y')
        ];

        return response()->json($details);
    }

    /**
     * Get auction panel statistics.
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'pending_requests' => League::whereNotNull('auction_access_requested_at')
                ->where('auction_access_granted', false)
                ->count(),
            'granted_access' => League::where('auction_access_granted', true)->count(),
            'total_leagues' => League::count(),
            'auction_enabled_percentage' => League::count() > 0 
                ? round((League::where('auction_access_granted', true)->count() / League::count()) * 100, 2) 
                : 0,
            'recent_requests' => League::whereNotNull('auction_access_requested_at')
                ->where('auction_access_granted', false)
                ->where('auction_access_requested_at', '>=', now()->subDays(7))
                ->count()
        ];

        return response()->json($stats);
    }
}