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
        // Get available players
        $availablePlayers = LeaguePlayer::with(['user', 'leagueTeam.team'])
            ->where('league_id', $league->id)
            ->where('status', 'available')
            ->orderBy('base_price', 'desc')
            ->paginate(10);

        // Get teams in the league
        $leagueTeams = LeagueTeam::with(['team'])
            ->where('league_id', $league->id)
            ->get();

        // Get user's team if they have one
        $userTeam = null;
        if (Auth::check()) {
            $userTeam = LeagueTeam::with(['team'])
                ->whereHas('team', function ($query) {
                    $query->where('owner_id', Auth::id());
                })
                ->where('league_id', $league->id)
                ->first();
        }

        return view('auction.index', compact('availablePlayers', 'leagueTeams', 'userTeam', 'league'));
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
                'message' => 'Bid placed successfully!'
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
            $winningBid = AuctionBid::where('league_player_id', $request->league_player_id)
                ->where('status', 'ask')
                ->orderBy('amount', 'desc')
                ->first();

            if (!$winningBid) {
                return response()->json(['error' => 'No bids found for this player'], 400);
            }

            // Mark the winning bid as won
            $winningBid->update(['status' => 'won']);

            // Mark all other bids as lost (delete them)
            AuctionBid::where('league_player_id', $request->league_player_id)
                ->where('id', '!=', $winningBid->id)
                ->where('status', 'ask')
                ->delete();

            // Update player status and assign to team
            $leaguePlayer->update([
                'league_team_id' => $winningBid->league_team_id,
                'status' => 'sold',
            ]);

            // Deduct amount from winning team's wallet
            $winningTeam = LeagueTeam::find($winningBid->league_team_id);
            $winningTeam->decrement('wallet_balance', $winningBid->amount);

            // Update the winning bid status to 'won'
            $winningBid->update(['status' => 'won']);

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
     * Get current bids for a player.
     */
    public function getCurrentBids(Request $request, League $league)
    {
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
        ]);

        $leaguePlayerId = $request->league_player_id;
        $leaguePlayer = LeaguePlayer::find($leaguePlayerId);
        
        // Verify the player belongs to this league
        if (!$leaguePlayer || $leaguePlayer->league_id !== $league->id) {
            return response()->json(['error' => 'Invalid player for this league'], 400);
        }

                    $bids = Auction::with(['leagueTeam.team'])
            ->where('league_player_id', $leaguePlayerId)
            ->where('status', 'ask')
            ->orderBy('amount', 'desc')
            ->get();

        return response()->json($bids);
    }
    
    /**
     * Mark a player as unsold (skip).
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

            // Delete all existing bids for this player
            Auction::where('league_player_id', $request->league_player_id)
                ->where('status', 'ask')
                ->delete();

            // Update player status to unsold
            $leaguePlayer->update([
                'status' => 'unsold',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Player marked as unsold'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to mark player as unsold: ' . $e->getMessage()], 500);
        }
    }
}
