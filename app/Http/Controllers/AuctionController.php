<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
    /**
     * Display the auction bidding page.
     */
    public function index(League $league)
    {
        $leaguePlayers = LeaguePlayer::where('league_id',$league->id)->get();
        return view('auction.index',compact('leaguePlayers'));
    }

    /**
     * Start the auction.
     */
    public function startAuction(Request $request, League $league)
    {
        // Check if user is the league organizer
        if ($league->user_id !== Auth::id()) {
            return response()->json(['error' => 'Only the league organizer can start the auction'], 403);
        }

        $league->startAuction();

        return response()->json([
            'success' => true,
            'message' => 'Auction started successfully!',
            'auction_status' => 'active'
        ]);
    }

    /**
     * Pause the auction.
     */
    public function pauseAuction(Request $request, League $league)
    {
        // Check if user is the league organizer
        if ($league->user_id !== Auth::id()) {
            return response()->json(['error' => 'Only the league organizer can pause the auction'], 403);
        }

        $league->pauseAuction();

        return response()->json([
            'success' => true,
            'message' => 'Auction paused successfully!',
            'auction_status' => 'paused'
        ]);
    }

    /**
     * End the auction.
     */
    public function endAuction(Request $request, League $league)
    {
        // Check if user is the league organizer
        if ($league->user_id !== Auth::id()) {
            return response()->json(['error' => 'Only the league organizer can end the auction'], 403);
        }

        $league->endAuction();

        return response()->json([
            'success' => true,
            'message' => 'Auction ended successfully!',
            'auction_status' => 'ended'
        ]);
    }

    /**
     * Update auction settings.
     */
    public function updateAuctionSettings(Request $request, League $league)
    {
        // Check if user is the league organizer
        if ($league->user_id !== Auth::id()) {
            return response()->json(['error' => 'Only the league organizer can update auction settings'], 403);
        }

        $request->validate([
            'bid_increment_type' => 'required|in:predefined,custom',
            'custom_bid_increment' => 'nullable|numeric|min:1',
            'predefined_increments' => 'nullable|array',
        ]);

        $league->update([
            'bid_increment_type' => $request->bid_increment_type,
            'custom_bid_increment' => $request->custom_bid_increment,
            'predefined_increments' => $request->predefined_increments,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Auction settings updated successfully!'
        ]);
    }

    /**
     * Place a bid on a player.
     */
    public function placeBid(Request $request, League $league)
    {
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
            'league_team_id' => 'required|exists:league_teams,id',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $leaguePlayer = LeaguePlayer::find($request->league_player_id);
            $leagueTeam = LeagueTeam::find($request->league_team_id);

            // Verify the team belongs to this league
            if ($leagueTeam->league_id !== $league->id) {
                return response()->json(['error' => 'Invalid team for this league'], 400);
            }

            // Verify the player belongs to this league
            if ($leaguePlayer->league_id !== $league->id) {
                return response()->json(['error' => 'Invalid player for this league'], 400);
            }

            // Check if team has enough wallet balance
            if ($leagueTeam->wallet_balance < $request->amount) {
                return response()->json(['error' => 'Insufficient wallet balance'], 400);
            }

            // Check if bid amount is higher than base price
            if ($request->amount < $leaguePlayer->base_price) {
                return response()->json(['error' => 'Bid amount must be at least the base price'], 400);
            }

            // Get current highest bid
            $highestBid = Auction::where('league_player_id', $request->league_player_id)
                ->where('status', 'ask')
                ->orderBy('amount', 'desc')
                ->first();

            // Check if bid is higher than current highest
            if ($highestBid && $request->amount <= $highestBid->amount) {
                return response()->json(['error' => 'Bid amount must be higher than current highest bid'], 400);
            }

            // Check if bid meets minimum increment requirement
            $nextMinimumBid = $highestBid ? $league->getNextMinimumBid($highestBid->amount) : $leaguePlayer->base_price;
            if ($request->amount < $nextMinimumBid) {
                return response()->json(['error' => 'Bid amount must be at least ₹' . $nextMinimumBid], 400);
            }

            // Create the bid
            $bid = Auction::create([
                'league_player_id' => $request->league_player_id,
                'league_team_id' => $request->league_team_id,
                'amount' => $request->amount,
                'status' => 'ask',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'bid' => $bid->load(['leagueTeam.team']),
                'message' => 'Bid placed successfully!',
                'next_minimum_bid' => $league->getNextMinimumBid($request->amount)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to place bid: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Accept the highest bid and sell the player.
     */
    public function acceptBid(Request $request, League $league)
    {
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
        ]);

        try {
            DB::beginTransaction();

            $leaguePlayer = LeaguePlayer::find($request->league_player_id);

            // Verify the player belongs to this league
            if ($leaguePlayer->league_id !== $league->id) {
                return response()->json(['error' => 'Invalid player for this league'], 400);
            }

            // Get the highest bid
            $winningBid = Auction::where('league_player_id', $request->league_player_id)
                ->where('status', 'ask')
                ->orderBy('amount', 'desc')
                ->first();

            if (!$winningBid) {
                return response()->json(['error' => 'No bids found for this player'], 400);
            }

            // Mark the winning bid as won
            $winningBid->update(['status' => 'won']);

            // Mark all other bids as lost (delete them)
            Auction::where('league_player_id', $request->league_player_id)
                ->where('id', '!=', $winningBid->id)
                ->where('status', 'ask')
                ->delete();

            // Update player status and assign to team
            $leaguePlayer->update([
                'league_team_id' => $winningBid->league_team_id,
                'status' => 'sold',
                'bid_price' => $winningBid->amount,
            ]);

            // Deduct amount from winning team's wallet
            $winningTeam = LeagueTeam::find($winningBid->league_team_id);
            $winningTeam->decrement('wallet_balance', $winningBid->amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'winning_bid' => $winningBid->load(['leagueTeam.team']),
                'message' => 'Player sold successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to accept bid: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Skip player (mark as unsold).
     */
    public function skipPlayer(Request $request, League $league)
    {
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
        ]);

        try {
            DB::beginTransaction();

            $leaguePlayer = LeaguePlayer::find($request->league_player_id);

            // Verify the player belongs to this league
            if ($leaguePlayer->league_id !== $league->id) {
                return response()->json(['error' => 'Invalid player for this league'], 400);
            }

            // Delete all bids for this player
            Auction::where('league_player_id', $request->league_player_id)
                ->where('status', 'ask')
                ->delete();

            // Mark player as unsold
            $leaguePlayer->update([
                'status' => 'unsold',
                'league_team_id' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Player marked as unsold successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to skip player: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get current bids for a player.
     */
    public function getCurrentBids(Request $request, League $league)
    {
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
        ]);

        $bids = Auction::where('league_player_id', $request->league_player_id)
            ->where('status', 'ask')
            ->with(['leagueTeam.team'])
            ->orderBy('amount', 'desc')
            ->get();

        return response()->json($bids);
    }

    /**
     * Get auction statistics.
     */
    public function getAuctionStats(League $league)
    {
        $stats = $league->getAuctionStats();
        
        return response()->json($stats);
    }
}
