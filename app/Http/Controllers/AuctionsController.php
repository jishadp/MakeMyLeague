<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\AuctionAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionsController extends Controller
{
    protected $auctionAccessService;

    public function __construct(AuctionAccessService $auctionAccessService)
    {
        $this->auctionAccessService = $auctionAccessService;
    }

    /**
     * Display the auctions page for team owners and auctioneers.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get leagues where user can participate in auctions
        $auctionLeagues = collect();
        
        // Get leagues where user is a team owner
        $ownedLeagueTeams = \App\Models\LeagueTeam::whereHas('team', function($query) use ($user) {
            $query->whereHas('owners', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })->with(['league', 'team'])->get();
        
        foreach ($ownedLeagueTeams as $leagueTeam) {
            $league = $leagueTeam->league;
            if (!$auctionLeagues->contains('id', $league->id)) {
                $auctionLeagues->push([
                    'league' => $league,
                    'role' => 'team_owner',
                    'league_team' => $leagueTeam,
                    'can_bid' => $this->auctionAccessService->canUserBidInLeague($user, $league),
                    'auction_status' => $this->getAuctionStatus($league)
                ]);
            }
        }
        
        // Get leagues where user is assigned as auctioneer
        $auctioneerAssignments = \App\Models\TeamAuctioneer::where('auctioneer_id', $user->id)
            ->where('status', 'active')
            ->with(['league', 'leagueTeam.team'])
            ->get();
        
        foreach ($auctioneerAssignments as $assignment) {
            $league = $assignment->league;
            $leagueTeam = $assignment->leagueTeam;
            
            if (!$auctionLeagues->contains('id', $league->id)) {
                $auctionLeagues->push([
                    'league' => $league,
                    'role' => 'auctioneer',
                    'league_team' => $leagueTeam,
                    'can_bid' => $this->auctionAccessService->canUserBidInLeague($user, $league),
                    'auction_status' => $this->getAuctionStatus($league)
                ]);
            }
        }
        
        // Get leagues where user is an organizer
        $organizedLeagues = $user->organizedLeagues()->where('status', 'approved')->get();
        
        foreach ($organizedLeagues as $league) {
            if (!$auctionLeagues->contains('id', $league->id)) {
                $auctionLeagues->push([
                    'league' => $league,
                    'role' => 'organizer',
                    'league_team' => null,
                    'can_bid' => $this->auctionAccessService->canUserBidInLeague($user, $league),
                    'auction_status' => $this->getAuctionStatus($league)
                ]);
            }
        }
        
        return view('auctions.index', compact('auctionLeagues'));
    }
    
    /**
     * Get auction status for a league.
     */
    private function getAuctionStatus($league)
    {
        if ($league->status === 'auction_completed') {
            return 'completed';
        }
        
        if ($league->auction_active) {
            return 'live';
        }
        
        if ($league->hasAuctionAccess()) {
            return 'ready';
        }
        
        return 'pending';
    }
}
