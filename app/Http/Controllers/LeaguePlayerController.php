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
        $query = LeaguePlayer::with(['leagueTeam.team', 'user', 'user.position'])
            ->where('league_id', $league->id)
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
        $availablePlayers = User::whereNotNull('position_id')
            ->whereNotIn('id', function($query) use ($league) {
                $query->select('user_id')
                      ->from('league_players')
                      ->where('league_id', $league->id);
            })
            ->with('position')
            ->get();

        return view('league-players.create', compact('league', 'leagueTeams', 'availablePlayers'));
    }
    
    /**
     * Show the form for bulk creating league players.
     */
    public function bulkCreate(League $league): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();

        // Get players not already in this league
        $availablePlayers = User::whereNotNull('position_id')
            ->whereNotIn('id', function($query) use ($league) {
                $query->select('user_id')
                      ->from('league_players')
                      ->where('league_id', $league->id);
            })
            ->with('position')
            ->get();

        return view('league-players.bulk-create', compact('league', 'leagueTeams', 'availablePlayers'));
    }

    /**
     * Store a newly created league player.
     */
    public function store(Request $request, League $league)
    {
        // Determine validation rules based on retention status
        $isRetention = $request->has('retention') && $request->retention;
        
        $validationRules = [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $league) {
                    $exists = LeaguePlayer::where('user_id', $value)
                        ->where('league_id', $league->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('This player is already registered in this league.');
                    }
                },
            ],
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
        ];
        
        // Conditional validation rules
        if ($isRetention) {
            // For retention players: team is required, base price is not
            $validationRules['league_team_id'] = [
                'required',
                'exists:league_teams,id',
                function ($attribute, $value, $fail) use ($league) {
                    $leagueTeam = LeagueTeam::find($value);
                    if (!$leagueTeam || $leagueTeam->league_id !== $league->id) {
                        $fail('Please select a valid team for retention player.');
                    }
                },
            ];
            $validationRules['base_price'] = 'nullable|numeric|min:0';
        } else {
            // For non-retention players: team is optional, base price is required
            $validationRules['league_team_id'] = 'nullable|exists:league_teams,id';
            $validationRules['base_price'] = 'required|numeric|min:0';
        }
        
        $request->validate($validationRules);

        // Prepare data for creation
        $data = $request->all();
        $data['league_id'] = $league->id;
        
        // Handle retention player logic
        if ($isRetention) {
            $data['retention'] = true;
            $data['base_price'] = 0; // Set base price to 0 for retention players
        } else {
            $data['retention'] = false;
        }
        
        LeaguePlayer::create($data);

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', 'Player added to league successfully!');
    }
    
    /**
     * Bulk store multiple league players.
     */
    public function bulkStore(Request $request, League $league)
    {
        $request->validate([
            'league_team_id' => 'nullable|exists:league_teams,id',
            'user_ids' => 'required|array',
            'user_ids.*' => [
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $league) {
                    $exists = LeaguePlayer::where('user_id', $value)
                        ->where('league_id', $league->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail("Player with ID {$value} is already registered in this league.");
                    }
                },
            ],
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

        $addedCount = 0;
        
        foreach ($request->user_ids as $userId) {
            LeaguePlayer::create([
                'user_id' => $userId,
                'league_id' => $league->id,
                'league_team_id' => $request->league_team_id,
                'base_price' => $request->base_price,
                'status' => $request->status,
                'retention' => false
            ]);
            
            $addedCount++;
        }

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', "{$addedCount} players added to league successfully!");
    }

    /**
     * Display the specified league player.
     */
    public function show(League $league, LeaguePlayer $leaguePlayer): View
    {
        $leaguePlayer->load(['leagueTeam.team', 'user', 'user.position']);

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

        // Ensure we don't accidentally change the league_id
        $data = $request->all();
        $data['league_id'] = $league->id;
        $leaguePlayer->update($data);

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
            // Set specific messages for status changes
            if (isset($validatedData['status'])) {
                switch ($validatedData['status']) {
                    case 'available':
                        $successMessage = 'Player approved successfully!';
                        break;
                    case 'unsold':
                        $successMessage = 'Player rejected successfully!';
                        break;
                    case 'sold':
                        $successMessage = 'Player marked as sold successfully!';
                        break;
                    default:
                        $successMessage = 'Player status updated successfully!';
                }
            } else {
                $successMessage = 'Player status updated successfully!';
            }
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
        
        // Check if the request is coming from the players index page
        if ($referer && strpos($referer, '/players') !== false && strpos($referer, '/players/' . $leaguePlayer->slug) === false) {
            return redirect()
                ->route('league-players.index', $league)
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
                ->where('league_id', $league->id)
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

    /**
     * Handle player registration request for a league.
     */
    public function requestRegistration(Request $request, League $league)
    {
        // Check if user is authenticated and is a player
        if (!auth()->check() || !auth()->user()->isPlayer()) {
            return response()->json([
                'success' => false,
                'message' => 'Only players can register for leagues.'
            ], 403);
        }

        // Check if league is active or pending (both allow player registration)
        if (!in_array($league->status, ['active', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Player registration is only available for active or pending leagues.'
            ], 400);
        }

        $user = auth()->user();

        // Validate position selection
        $request->validate([
            'position_id' => 'required|exists:game_positions,id'
        ]);

        // Check if player is already registered in this league
        $existingPlayer = LeaguePlayer::where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->first();

        if ($existingPlayer) {
            return response()->json([
                'success' => false,
                'message' => 'You are already registered in this league.'
            ], 400);
        }

        // Update user's position if different from current
        if ($user->position_id != $request->position_id) {
            $user->update(['position_id' => $request->position_id]);
        }

        // Create the league player with pending status
        LeaguePlayer::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'base_price' => $league->player_reg_fee ?? 0,
            'retention' => false,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration request submitted successfully! Please wait for approval from the organizers.'
        ]);
    }
}
