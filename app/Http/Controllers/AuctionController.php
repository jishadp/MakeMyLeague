<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Player;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuctionController extends Controller
{
    use AuthorizesRequests;

    public function show(League $league)
    {
        $this->authorize('view', $league);
        
        $leagueTeams = $league->leagueTeams()->get();
        $availablePlayers = $league->leagueAvailablePlayers()->get();
        
        $stats = [
            'sold' => $league->leaguePlayers()->wherePivot('auction_status', 'sold')->count(),
            'unsold' => $league->leaguePlayers()->wherePivot('auction_status', 'unsold')->count(),
            'skip' => $league->leaguePlayers()->wherePivot('auction_status', 'skip')->count()
        ];
        
        return view('auction.show', compact('league', 'leagueTeams', 'availablePlayers', 'stats'));
    }

    public function setup(League $league)
    {
        $this->authorize('update', $league);
        return view('auction.setup', compact('league'));
    }

    public function start(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        // Validate basic inputs first
        $validatedData = $request->validate([
            'min_players_needed' => 'required|integer|min:1|max:25',
            'min_bid_amount' => 'required|numeric',
            'purse_balance' => 'required|numeric',
        ]);
        
        // Custom validation for min_bid_amount
        if ($request->min_bid_amount < 100) {
            return back()
                ->withInput()
                ->withErrors(['min_bid_amount' => 'Minimum bid amount must be at least ₹100']);
        }
        
        // Calculate minimum required purse balance
        $minRequiredPurse = $request->min_players_needed * $request->min_bid_amount;
        
        // Custom validation for purse_balance
        if ($request->purse_balance < $minRequiredPurse) {
            $formattedAmount = number_format($minRequiredPurse);
            $formattedBid = number_format($request->min_bid_amount);
            
            return back()
                ->withInput()
                ->withErrors(['purse_balance' => "Purse balance must be at least ₹{$formattedAmount} ({$request->min_players_needed} players × ₹{$formattedBid} minimum bid)"]);
        }

        $league->update([
            'purse_balance' => $request->purse_balance,
            'min_players_needed' => $request->min_players_needed,
            'min_bid_amount' => $request->min_bid_amount,
            'auction_started' => true
        ]);

        // Initialize team purses
        DB::table('league_team')
            ->where('league_id', $league->id)
            ->update([
                'initial_purse_balance' => $request->purse_balance,
                'purse_balance' => $request->purse_balance
            ]);

        return redirect()->route('auction.show', $league)
            ->with('success', 'Auction started successfully!');
    }

    public function reset(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        // Reset all auction data
        DB::table('league_player')->where('league_id', $league->id)->delete();
        DB::table('league_team')->where('league_id', $league->id)
            ->update([
                'purse_balance' => DB::raw('initial_purse_balance'),
                'updated_at' => now()
            ]);
        
        // Also update the auction_started status to false if this is a restart
        if ($request->has('restart')) {
            $league->update(['auction_started' => false]);
            return redirect()->route('auction.setup', $league)
                ->with('success', 'Auction restarted successfully! Please configure auction settings.');
        }
        
        return redirect()->route('auction.show', $league)
            ->with('success', 'Auction reset successfully!');
    }
    
    public function public(League $league)
    {
        // Get league teams
        $leagueTeams = $league->leagueTeams()->get();
        
        // Get recent auction results (sold players)
        $recentSoldPlayers = $league->leaguePlayers()
                                ->wherePivot('auction_status', 'sold')
                                ->orderBy('league_player.updated_at', 'desc')
                                ->take(10)
                                ->get();
        
        // Get stats for the auction
        $stats = [
            'sold' => $league->leaguePlayers()->wherePivot('auction_status', 'sold')->count(),
            'unsold' => $league->leaguePlayers()->wherePivot('auction_status', 'unsold')->count(),
            'skip' => $league->leaguePlayers()->wherePivot('auction_status', 'skip')->count(),
            'total_spent' => $league->leaguePlayers()->wherePivot('auction_status', 'sold')->sum('league_player.bid_amount')
        ];
        
        return view('auction.public', compact('league', 'leagueTeams', 'recentSoldPlayers', 'stats'));
    }

    public function bid(Request $request, League $league)
    {
        $this->authorize('update', $league);
        
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'league_team_id' => $request->action === 'sell' ? 'required|exists:league_team,id' : 'nullable',
            'bid_amount' => $request->action === 'sell' ? 'required|numeric|min:' . $league->min_bid_amount : 'nullable',
            'action' => 'required|in:sell,unsold,skip'
        ]);

        try {
            // Begin a database transaction for atomicity
            DB::beginTransaction();
            
            // For sell action, check and update team purse
            if ($request->action === 'sell') {
                $team = DB::table('league_team')
                    ->where('id', $request->league_team_id)
                    ->where('league_id', $league->id)
                    ->first();
                    
                if (!$team) {
                    return back()->withErrors(['league_team_id' => 'Selected team not found']);
                }
                
                if ($team->purse_balance < $request->bid_amount) {
                    return back()->withErrors(['bid_amount' => 'Insufficient purse balance']);
                }
                
                // Update team purse balance
                $updated = DB::table('league_team')
                    ->where('id', $request->league_team_id)
                    ->decrement('purse_balance', $request->bid_amount);
                    
                if (!$updated) {
                    throw new \Exception('Failed to update team purse balance');
                }
            }

            // Update or insert league_player entry
            $leaguePlayerData = [
                'updated_at' => now()
            ];
            
            // Set additional fields based on action
            if ($request->action === 'sell') {
                $leaguePlayerData['league_team_id'] = $request->league_team_id;
                $leaguePlayerData['bid_amount'] = $request->bid_amount;
            } else {
                $leaguePlayerData['league_team_id'] = null;
                $leaguePlayerData['bid_amount'] = null;
            }
            
            // Always set auction status
            $leaguePlayerData['auction_status'] = $request->action;
            
            // Check if record exists
            $exists = DB::table('league_player')
                ->where('league_id', $league->id)
                ->where('player_id', $request->player_id)
                ->exists();
                
            if ($exists) {
                // Update existing record
                $updated = DB::table('league_player')
                    ->where('league_id', $league->id)
                    ->where('player_id', $request->player_id)
                    ->update($leaguePlayerData);
                    
                if (!$updated) {
                    throw new \Exception('Failed to update player auction status');
                }
            } else {
                // Insert new record
                $leaguePlayerData['league_id'] = $league->id;
                $leaguePlayerData['player_id'] = $request->player_id;
                $leaguePlayerData['created_at'] = now();
                
                $inserted = DB::table('league_player')->insert($leaguePlayerData);
                
                if (!$inserted) {
                    throw new \Exception('Failed to create player auction entry');
                }
            }
            
            // Commit the transaction
            DB::commit();
            
            // Get player and team details for success message
            $player = \App\Models\Player::find($request->player_id);
            $playerName = $player ? $player->name : 'Player';
            
            $successMessage = '';
            
            if ($request->action === 'sell') {
                $team = DB::table('league_team')
                    ->join('teams', 'league_team.team_id', '=', 'teams.id')
                    ->where('league_team.id', $request->league_team_id)
                    ->select('teams.name')
                    ->first();
                $teamName = $team ? $team->name : 'the team';
                
                $successMessage = "Sold! {$playerName} has been sold to {$teamName} for ₹" . number_format($request->bid_amount);
            } elseif ($request->action === 'unsold') {
                $successMessage = "Unsold! {$playerName} has been marked as unsold in this auction";
            } else {
                $successMessage = "Skipped! {$playerName} has been skipped for now";
            }
            
            return redirect()->route('auction.show', $league)
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            
            return back()->withErrors(['general' => 'Error processing auction: ' . $e->getMessage()]);
        }
    }
}
