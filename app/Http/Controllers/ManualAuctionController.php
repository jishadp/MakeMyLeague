<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualAuctionController extends Controller
{
    /**
     * Display the manual auction page.
     */
    public function index(League $league)
    {
        // Get available players for auction (those with status available, unsold, or skip, excluding retention players)
        $availablePlayers = LeaguePlayer::with(['user', 'user.role', 'leagueTeam.team'])
            ->where('league_id', $league->id)
            ->where(function($query) {
                $query->where('status', 'available')
                      ->orWhere('status', 'unsold')
                      ->orWhere('status', 'skip');
            })
            ->where('retention', false) // Exclude retention players
            ->orderBy('base_price', 'desc')
            ->paginate(20);

        // Get teams in the league
        $leagueTeams = LeagueTeam::with(['team'])
            ->where('league_id', $league->id)
            ->get();

        // Get recent auctions
        $recentAuctions = Auction::with(['player', 'leagueTeam.team', 'creator'])
            ->whereHas('leagueTeam', function ($query) use ($league) {
                $query->where('league_id', $league->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get player counts by status
        $playerCounts = LeaguePlayer::where('league_id', $league->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('auction.manual', compact('availablePlayers', 'leagueTeams', 'recentAuctions', 'league', 'playerCounts'));
    }

    /**
     * Store a manual auction transaction.
     */
    public function store(Request $request, League $league)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'league_team_id' => 'required|exists:league_teams,id',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check if the team has enough wallet balance
            $leagueTeam = LeagueTeam::find($request->league_team_id);
            if ($leagueTeam->wallet_balance < $request->amount) {
                return back()->withErrors(['amount' => 'Team does not have sufficient wallet balance.']);
            }

            // Verify the team belongs to this league
            if ($leagueTeam->league_id !== $league->id) {
                return back()->withErrors(['error' => 'Invalid team for this league.']);
            }

            // Update the league player status to sold and assign to team
            // Use the direct league_id field instead of going through leagueTeam relation
            $leaguePlayer = LeaguePlayer::where('user_id', $request->user_id)
                ->where('league_id', $league->id)
                ->first();

            if (!$leaguePlayer) {
                return back()->withErrors(['error' => 'Player not found in this league.']);
            }

            // Create the auction record
            $auction = Auction::create([
                'user_id' => $request->user_id,
                'league_team_id' => $request->league_team_id,
                'amount' => $request->amount,
                'created_by' => Auth::id(),
            ]);

            // Update player status to sold and assign to team
            $leaguePlayer->update([
                'league_team_id' => $request->league_team_id,
                'status' => 'sold',
            ]);

            // Deduct amount from team wallet
            $leagueTeam->decrement('wallet_balance', $request->amount);

            DB::commit();

            return redirect()->back()->with('success', 'Player successfully auctioned to the team!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to process auction: ' . $e->getMessage()]);
        }
    }

    /**
     * Update player status (sold/unsold/skip).
     */
    public function updatePlayerStatus(Request $request, League $league)
    {
        $request->validate([
            'player_ids' => 'required|array',
            'player_ids.*' => 'exists:league_players,id',
            'status' => 'required|in:sold,unsold,skip',
            'league_team_id' => 'required_if:status,sold|exists:league_teams,id',
            'amount' => 'required_if:status,sold|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->player_ids as $playerId) {
                $leaguePlayer = LeaguePlayer::find($playerId);
                
                // Verify the player belongs to this league
                if (!$leaguePlayer || $leaguePlayer->league_id !== $league->id) {
                    continue;
                }

                if ($request->status === 'sold') {
                    $leagueTeam = LeagueTeam::find($request->league_team_id);
                    
                    // Check wallet balance
                    if ($leagueTeam->wallet_balance < $request->amount) {
                        return back()->withErrors(['amount' => 'Team does not have sufficient wallet balance.']);
                    }

                    // Create auction record
                    Auction::create([
                        'user_id' => $leaguePlayer->user_id,
                        'league_team_id' => $request->league_team_id,
                        'amount' => $request->amount,
                        'created_by' => Auth::id(),
                    ]);

                    // Update player
                    $leaguePlayer->update([
                        'league_team_id' => $request->league_team_id,
                        'status' => 'sold',
                    ]);

                    // Deduct from wallet
                    $leagueTeam->decrement('wallet_balance', $request->amount);
                } else {
                    // For unsold or skip, just update status
                    $leaguePlayer->update([
                        'status' => $request->status,
                    ]);
                }
            }

            DB::commit();

            $message = match($request->status) {
                'sold' => 'Player(s) successfully sold!',
                'unsold' => 'Player(s) marked as unsold.',
                'skip' => 'Player(s) skipped for now.',
            };

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update player status: ' . $e->getMessage()]);
        }
    }

    /**
     * Search players for auction.
     */
    public function searchPlayers(Request $request, League $league)
    {
        $search = $request->get('search');

        $players = LeaguePlayer::with(['user', 'leagueTeam.team'])
            ->where('league_id', $league->id)
            ->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('mobile', 'like', '%' . $search . '%');
            })
            ->where('status', 'available')
            ->take(10)
            ->get();

        return response()->json($players);
    }

    /**
     * Get team wallet balance.
     */
    public function getTeamWallet($teamSlug, League $league)
    {
        $leagueTeam = LeagueTeam::whereHas('team', function($query) use ($teamSlug) {
                $query->where('slug', $teamSlug);
            })
            ->where('league_id', $league->id)
            ->first();
            
        return response()->json([
            'wallet_balance' => $leagueTeam ? $leagueTeam->wallet_balance : 0
        ]);
    }
}
