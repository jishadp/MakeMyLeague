<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaguePlayerController extends Controller
{
    /**
     * Display a listing of league players.
     */
    public function index(League $league): View
    {
        $query = LeaguePlayer::with(['leagueTeam.team', 'user', 'user.role'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('retention'), function($query, $retention) {
                $query->where('retention', $retention === 'true');
            })
            ->when(request('team') && request('team') !== 'unassigned', function($query, $teamSlug) {
                $query->whereHas('leagueTeam.team', function($subQuery) use ($teamSlug) {
                    $subQuery->where('slug', $teamSlug);
                });
            })
            ->when(request('team') === 'unassigned', function($query) {
                $query->whereNull('league_team_id');
            });

        // If no specific team filter, ensure we only get players for this league
        if (!request('team')) {
            $query->where(function($subQuery) use ($league) {
                $subQuery->whereHas('leagueTeam', function($q) use ($league) {
                    $q->where('league_id', $league->id);
                })
                ->orWhereNull('league_team_id'); // Include players without a team
            });
        }

        $leaguePlayers = $query->orderBy('base_price', 'desc')
            ->paginate(15);

        // Get available teams for filtering
        $teams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get()
            ->pluck('team');

        // Get status counts for all players in the league
        $statusQuery = LeaguePlayer::where(function($query) use ($league) {
            $query->whereHas('leagueTeam', function($q) use ($league) {
                $q->where('league_id', $league->id);
            })
            ->orWhereNull('league_team_id');
        });
        
        $statusCounts = $statusQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Count players without a team
        $unassignedCount = LeaguePlayer::whereNull('league_team_id')
            ->count();

        return view('league-players.index', compact('league', 'leaguePlayers', 'teams', 'statusCounts', 'unassignedCount'));
    }

    /**
     * Show the form for creating a new league player.
     */
    public function create(League $league): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();

        // Get players not already in this league
        $availablePlayers = User::whereNotNull('role_id')
            ->whereNotIn('id', function($query) use ($league) {
                $query->select('user_id')
                      ->from('league_players')
                      ->join('league_teams', 'league_players.league_team_id', '=', 'league_teams.id')
                      ->where('league_teams.league_id', $league->id);
            })
            ->with('role')
            ->get();

        return view('league-players.create', compact('league', 'leagueTeams', 'availablePlayers'));
    }

    /**
     * Store a newly created league player.
     */
    public function store(Request $request, League $league)
    {
        $request->validate([
            'league_team_id' => 'nullable|exists:league_teams,id',
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $league) {
                    $exists = LeaguePlayer::whereHas('leagueTeam', function($query) use ($league) {
                        $query->where('league_id', $league->id);
                    })->where('user_id', $value)->exists();
                    
                    if ($exists) {
                        $fail('This player is already registered in this league.');
                    }
                },
            ],
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
            'base_price' => 'required|numeric|min:0',
        ]);

        // Validate league team belongs to current league if provided
        if ($request->league_team_id) {
            $leagueTeam = LeagueTeam::findOrFail($request->league_team_id);
            if ($leagueTeam->league_id !== $league->id) {
                return back()->withErrors(['league_team_id' => 'Invalid team selected.']);
            }
        }

        LeaguePlayer::create($request->all());

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', 'Player added to league successfully!');
    }

    /**
     * Display the specified league player.
     */
    public function show(League $league, LeaguePlayer $leaguePlayer): View
    {
        $leaguePlayer->load(['leagueTeam.team', 'user', 'user.role']);

        return view('league-players.show', compact('league', 'leaguePlayer'));
    }

    /**
     * Show the form for editing the specified league player.
     */
    public function edit(League $league, LeaguePlayer $leaguePlayer): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();

        return view('league-players.edit', compact('league', 'leaguePlayer', 'leagueTeams'));
    }

    /**
     * Update the specified league player.
     */
    public function update(Request $request, League $league, LeaguePlayer $leaguePlayer)
    {
        $request->validate([
            'league_team_id' => 'nullable|exists:league_teams,id',
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
            'base_price' => 'required|numeric|min:0',
        ]);

        // Validate league team belongs to current league if provided
        if ($request->league_team_id) {
            $leagueTeam = LeagueTeam::findOrFail($request->league_team_id);
            if ($leagueTeam->league_id !== $league->id) {
                return back()->withErrors(['league_team_id' => 'Invalid team selected.']);
            }
        }

        $leaguePlayer->update($request->all());

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', 'League player updated successfully!');
    }

    /**
     * Remove the specified league player.
     */
    public function destroy(League $league, LeaguePlayer $leaguePlayer)
    {
        // Store the team ID before deletion for redirection
        $leagueTeamId = $leaguePlayer->league_team_id;
        $leagueTeam = LeagueTeam::find($leagueTeamId);
        
        $playerName = $leaguePlayer->user->name;
        $leaguePlayer->delete();

        // Check if the request is coming from team page
        $referer = request()->headers->get('referer');
        if ($referer && $leagueTeam && strpos($referer, 'teams/' . $leagueTeam->slug) !== false) {
            return redirect()
                ->route('league-teams.show', [$league, $leagueTeam])
                ->with('success', "Player {$playerName} removed from team successfully!");
        }

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', "Player {$playerName} removed from league successfully!");
    }

    /**
     * Update player status.
     */
    public function updateStatus(Request $request, League $league, LeaguePlayer $leaguePlayer)
    {
        $validatedData = $request->validate([
            'status' => 'nullable|in:pending,available,sold,unsold,skip',
            'retention' => 'nullable|boolean',
        ]);

        $updates = [];
        
        if (isset($validatedData['status'])) {
            $updates['status'] = $validatedData['status'];
        }
        
        if (isset($validatedData['retention'])) {
            $updates['retention'] = $validatedData['retention'];
            
            // If removing retention, check where the request is coming from for proper redirection
            if ($validatedData['retention'] == 0) {
                $successMessage = 'Player retention status removed successfully.';
            } else {
                $successMessage = 'Player status updated successfully!';
            }
        } else {
            $successMessage = 'Player status updated successfully!';
        }

        $leaguePlayer->update($updates);

        // Check if the request is coming from team page
        $referer = request()->headers->get('referer');
        $leagueTeam = $leaguePlayer->leagueTeam;
        
        if ($referer && $leagueTeam && strpos($referer, 'teams/' . $leagueTeam->slug) !== false) {
            return redirect()
                ->route('league-teams.show', [$league, $leagueTeam])
                ->with('success', $successMessage);
        }
        
        if ($referer && strpos($referer, 'players/' . $leaguePlayer->slug) !== false) {
            return redirect()
                ->route('league-players.show', [$league, $leaguePlayer])
                ->with('success', $successMessage);
        }

        return back()->with('success', $successMessage);
    }

    /**
     * Bulk update player statuses.
     */
    public function bulkUpdateStatus(Request $request, League $league)
    {
        $request->validate([
            'player_ids' => 'required|array',
            'player_ids.*' => 'exists:league_players,id',
            'status' => 'nullable|in:pending,available,sold,unsold,skip',
            'retention' => 'nullable|array',
            'retention.*' => 'boolean',
        ]);

        // Get the referer to determine which team page we came from
        $referer = request()->headers->get('referer');
        $teamSlug = null;
        
        if ($referer && preg_match('/teams\/([^\/]+)/', $referer, $matches)) {
            $teamSlug = $matches[1];
            $leagueTeam = LeagueTeam::where('slug', $teamSlug)
                                    ->where('league_id', $league->id)
                                    ->first();
        }

        // If status is provided, update all player statuses
        if ($request->has('status')) {
            LeaguePlayer::whereIn('id', $request->player_ids)
                ->whereHas('leagueTeam', function($query) use ($league) {
                    $query->where('league_id', $league->id);
                })
                ->update(['status' => $request->status]);
        }

        // If retention is provided, update individual player retention status
        if ($request->has('retention')) {
            foreach ($request->player_ids as $playerId) {
                if (isset($request->retention[$playerId])) {
                    $leaguePlayer = LeaguePlayer::find($playerId);
                    
                    if (!$leaguePlayer) continue;
                    
                    // For retained players from other teams, move them to the current team
                    if ((bool)$request->retention[$playerId] && $leagueTeam && $leaguePlayer->league_team_id != $leagueTeam->id) {
                        $leaguePlayer->update([
                            'league_team_id' => $leagueTeam->id,
                            'retention' => true
                        ]);
                    } else {
                        // Just update the retention status
                        $leaguePlayer->update([
                            'retention' => (bool)$request->retention[$playerId]
                        ]);
                    }
                }
            }
        }

        $count = count($request->player_ids);
        return back()->with('success', "{$count} players updated successfully!");
    }
}
