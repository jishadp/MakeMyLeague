<?php

namespace App\Http\Controllers;

use App\Models\GamePosition;
use App\Models\LocalBody;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaguePlayerController extends Controller
{
    /**
     * Display a listing of league players.
     */
    public function index(League $league): View
    {
        $statusFilter = request()->has('status') ? request()->input('status') : 'available';

        $query = LeaguePlayer::with(['leagueTeam.team', 'user', 'user.position'])
            ->where('league_id', $league->id)
            ->when($statusFilter !== '' && $statusFilter !== null, function($query) use ($statusFilter) {
                if ($statusFilter === 'available') {
                    $query->whereIn('status', ['available', 'auctioning']);
                } else {
                    $query->where('status', $statusFilter);
                }
            })
            ->when(request('team') && request('team') !== 'unassigned', function($query, $teamSlug) {
                $query->whereHas('leagueTeam.team', function($subQuery) use ($teamSlug) {
                    $subQuery->where('slug', $teamSlug);
                });
            })
            ->when(request('team') === 'unassigned', function($query) {
                $query->whereNull('league_team_id');
            })
            ->when(request('search'), function($query, $search) {
                $query->whereHas('user', function($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });

        $retentionFilter = request('retention');
        if (is_array($retentionFilter)) {
            $normalized = array_values(array_filter($retentionFilter, function ($value) {
                return $value !== null && $value !== '';
            }));

            if (!empty($normalized)) {
                $boolValues = array_map(function ($value) {
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }, $normalized);

                $query->whereIn('retention', $boolValues);
            }
        } elseif ($retentionFilter !== null && $retentionFilter !== '') {
            $query->where('retention', filter_var($retentionFilter, FILTER_VALIDATE_BOOLEAN));
        }

        $leaguePlayers = $query->join('users', 'league_players.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('league_players.*')
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

        $league->loadMissing([
            'leaguePlayers.user.localBody',
            'leaguePlayers.leagueTeam.team',
        ]);

        $gamePositions = GamePosition::query()
            ->when($league->game_id, fn ($query) => $query->where('game_id', $league->game_id))
            ->orderBy('name')
            ->get();

        $playersWithoutRoleCount = LeaguePlayer::where('league_id', $league->id)
            ->whereHas('user', function($query) {
                $query->whereNull('position_id');
            })
            ->count();

        return view('league-players.index', compact(
            'league',
            'leaguePlayers',
            'teams',
            'statusCounts',
            'unassignedCount',
            'gamePositions',
            'statusFilter',
            'playersWithoutRoleCount'
        ));
    }

    /**
     * Show the form for creating a new league player.
     */
    public function create(League $league): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();

        // Get current player count and calculate remaining slots
        $currentPlayerCount = LeaguePlayer::where('league_id', $league->id)->count();
        $maxPlayers = $league->max_teams * $league->max_team_players;
        $remainingSlots = max(0, $maxPlayers - $currentPlayerCount);

        // Get players not already in this league
        $availablePlayers = User::whereNotIn('id', function($query) use ($league) {
                $query->select('user_id')
                      ->from('league_players')
                      ->where('league_id', $league->id);
            })
            ->with('position')
            ->get();

        return view('league-players.create', compact('league', 'leagueTeams', 'availablePlayers', 'remainingSlots', 'maxPlayers', 'currentPlayerCount'));
    }
    
    /**
     * Show the form for bulk creating league players.
     */
    public function bulkCreate(League $league): View
    {
        $leagueTeams = LeagueTeam::with('team')
            ->where('league_id', $league->id)
            ->get();
        $league->loadMissing('game.roles');
        $localBodies = LocalBody::orderBy('name')->get();
        $gamePositions = $league->game?->roles?->sortBy('name') ?? collect();

        // Get current player count and calculate remaining slots
        $currentPlayerCount = LeaguePlayer::where('league_id', $league->id)->count();
        $maxPlayers = $league->max_teams * $league->max_team_players;
        $remainingSlots = max(0, $maxPlayers - $currentPlayerCount);

        // Get players not already in this league
        $availablePlayers = User::whereNotIn('id', function($query) use ($league) {
                $query->select('user_id')
                      ->from('league_players')
                      ->where('league_id', $league->id);
            })
            ->with('position')
            ->get();

        return view('league-players.bulk-create', compact('league', 'leagueTeams', 'availablePlayers', 'remainingSlots', 'maxPlayers', 'currentPlayerCount', 'localBodies', 'gamePositions'));
    }

    /**
     * Store a newly created league player.
     */
    public function store(Request $request, League $league)
    {
        // Check max players limit
        $currentPlayerCount = LeaguePlayer::where('league_id', $league->id)->count();
        $maxPlayers = $league->max_teams * $league->max_team_players;
        
        if ($currentPlayerCount >= $maxPlayers) {
            return back()->withErrors(['user_id' => "League is full. Maximum {$maxPlayers} players allowed."]);
        }

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
        // Check current player count and max limit
        $currentPlayerCount = LeaguePlayer::where('league_id', $league->id)->count();
        $maxPlayers = $league->max_teams * $league->max_team_players;
        $remainingSlots = max(0, $maxPlayers - $currentPlayerCount);

        $request->validate([
            'league_team_id' => 'nullable|exists:league_teams,id',
            'user_ids' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($remainingSlots) {
                    if (count($value) > $remainingSlots) {
                        $fail("You can only add {$remainingSlots} more player(s). Selected: " . count($value));
                    }
                },
            ],
            'user_ids.*' => [
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $league) {
                    $exists = LeaguePlayer::where('user_id', $value)
                        ->where('league_id', $league->id)
                        ->exists();
                    
                    if ($exists) {
                        $user = User::find($value);
                        $fail("Player '{$user->name}' is already registered in this league.");
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
     * Bulk import players for a league using location + pasted rows.
     */
    public function importByLocation(Request $request, League $league)
    {
        $currentPlayerCount = LeaguePlayer::where('league_id', $league->id)->count();
        $maxPlayers = $league->max_teams * $league->max_team_players;
        $remainingSlots = max(0, $maxPlayers - $currentPlayerCount);

        if ($remainingSlots === 0) {
            return back()->withErrors(['player_rows' => 'League is full. Cannot import more players.']);
        }

        $validated = $request->validate([
            'local_body_id' => 'required|exists:local_bodies,id',
            'player_rows' => 'required|string',
            'import_base_price' => 'nullable|numeric|min:0',
        ]);

        $basePrice = $validated['import_base_price'] ?? ($league->team_wallet_limit * 0.01);
        $playerRoleMap = GamePosition::where('game_id', $league->game_id)->get();
        $playerRoleLookup = $playerRoleMap->keyBy(function ($role) {
            return Str::lower($role->name);
        });

        $rawRows = $validated['player_rows'];
        $parsedRows = [];

        // Try JSON first
        $decoded = json_decode($rawRows, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
            foreach ($decoded as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }
                $parsedRows[] = [
                    'name' => trim($item['name'] ?? $item['Name'] ?? ''),
                    'mobile' => trim($item['mobile'] ?? $item['phone'] ?? ''),
                    'role' => trim($item['role'] ?? $item['position'] ?? ''),
                    'row' => $index + 1,
                    'source' => 'json',
                ];
            }
        }

        // Fallback: plain text lines "name, mobile, role"
        if (empty($parsedRows)) {
            $rows = preg_split("/\\r\\n|\\r|\\n/", $rawRows);
            foreach ($rows as $index => $row) {
                $row = trim($row);
                if ($row === '') {
                    continue;
                }
                [$name, $mobileRaw, $roleInput] = array_pad(array_map('trim', preg_split('/[,\\t]/', $row)), 3, null);
                $parsedRows[] = [
                    'name' => $name,
                    'mobile' => $mobileRaw,
                    'role' => $roleInput,
                    'row' => $index + 1,
                    'source' => 'lines',
                ];
            }
        }

        if (empty($parsedRows)) {
            return back()->withErrors(['player_rows' => 'No player rows found. Provide JSON array or comma separated lines.']);
        }

        $seenMobiles = [];
        $imported = 0;
        $skipped = [];

        foreach ($parsedRows as $row) {
            $mobile = $row['mobile'] ? preg_replace('/\\D+/', '', $row['mobile']) : null;

            if (!$row['name'] || !$mobile || !$row['role']) {
                $skipped[] = "Row {$row['row']}: missing name/mobile/role.";
                continue;
            }

            if (strlen($mobile) < 6) {
                $skipped[] = "Row {$row['row']}: mobile looks invalid.";
                continue;
            }

            if (isset($seenMobiles[$mobile])) {
                $skipped[] = "Row {$row['row']}: duplicate mobile {$mobile} in upload.";
                continue;
            }
            $seenMobiles[$mobile] = true;

            if ($remainingSlots <= 0) {
                $skipped[] = "Row {$row['row']}: league player limit reached.";
                break;
            }

            $normalizedRole = Str::lower($row['role']);
            $roleSlug = Str::slug($row['role']);
            $position = $playerRoleLookup->get($normalizedRole)
                ?? $playerRoleMap->first(function ($role) use ($normalizedRole) {
                    return Str::contains(Str::lower($role->name), $normalizedRole);
                })
                ?? $playerRoleMap->first(function ($role) use ($roleSlug) {
                    return Str::slug($role->name) === $roleSlug;
                });

            if (!$position) {
                $skipped[] = "Row {$row['row']}: unknown role '{$row['role']}'.";
                continue;
            }

            $user = User::where('mobile', $mobile)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $row['name'],
                    'mobile' => $mobile,
                    'country_code' => '+91',
                    'pin' => bcrypt((string) random_int(1000, 9999)),
                    'local_body_id' => $validated['local_body_id'],
                    'position_id' => $position->id,
                ]);
                $this->assignPlayerRole($user);
            } else {
                $needsUpdate = false;

                if (!$user->position_id) {
                    $user->position_id = $position->id;
                    $needsUpdate = true;
                }

                if (!$user->local_body_id) {
                    $user->local_body_id = $validated['local_body_id'];
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $user->save();
                }
            }

            $alreadyInLeague = LeaguePlayer::where('league_id', $league->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyInLeague) {
                $skipped[] = "Row {$row['row']}: {$mobile} already in league.";
                continue;
            }

            LeaguePlayer::create([
                'user_id' => $user->id,
                'league_id' => $league->id,
                'league_team_id' => null,
                'base_price' => $basePrice,
                'status' => 'available',
                'retention' => false
            ]);

            $imported++;
            $remainingSlots--;
        }

        $message = "{$imported} player(s) imported successfully.";
        if (count($skipped) > 0) {
            $message .= ' Some rows were skipped.';
        }

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', $message)
            ->with('warnings', $skipped);
    }

    /**
     * Ensure the player role exists for a user.
     */
    protected function assignPlayerRole(User $user): void
    {
        $playerRole = Role::where('name', User::ROLE_PLAYER)->first();

        if ($playerRole) {
            UserRole::firstOrCreate([
                'user_id' => $user->id,
                'role_id' => $playerRole->id,
            ]);
        }
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
     * Update a player's primary game role from the league roster.
     */
    public function updateRole(Request $request, League $league, LeaguePlayer $leaguePlayer)
    {
        if ($leaguePlayer->league_id !== $league->id) {
            abort(404);
        }

        $validated = $request->validate([
            'position_id' => 'required|exists:game_positions,id',
        ]);

        $position = GamePosition::query()
            ->when($league->game_id, fn ($query) => $query->where('game_id', $league->game_id))
            ->find($validated['position_id']);

        if (!$position) {
            return redirect()
                ->route('league-players.index', $league)
                ->withErrors(['position_id' => 'Selected role is not available for this league.']);
        }

        $player = $leaguePlayer->user;

        if (!$player) {
            return redirect()
                ->route('league-players.index', $league)
                ->withErrors(['player' => 'Unable to load the selected player.']);
        }

        $player->position_id = $position->id;
        $player->save();

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', sprintf("%s's role updated to %s.", $player->name, $position->name));
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
     * Bulk update base price for all available players in the league.
     */
    public function bulkUpdateBasePrice(Request $request, League $league)
    {
        $data = $request->validate([
            'base_price' => 'required|numeric|min:0',
        ]);
        
        $updated = LeaguePlayer::where('league_id', $league->id)
            ->where('status', 'available')
            ->update(['base_price' => $data['base_price']]);
        
        return redirect()
            ->route('league-players.index', $league)
            ->with('success', "{$updated} available player(s) updated with new base price.");
    }

    /**
     * Bulk assign "All-rounder" role to every player without a role in this league.
     */
    public function bulkAssignDefaultRole(League $league)
    {
        $defaultRoleName = 'All-rounder';

        $position = GamePosition::query()
            ->where('game_id', $league->game_id)
            ->whereRaw('LOWER(name) = ?', [Str::lower($defaultRoleName)])
            ->first();

        if (!$position) {
            return redirect()
                ->route('league-players.index', $league)
                ->withErrors(['position_id' => 'The All-rounder role is not available for this league.']);
        }

        $userIds = LeaguePlayer::where('league_id', $league->id)
            ->whereHas('user', function($query) {
                $query->whereNull('position_id');
            })
            ->pluck('user_id')
            ->unique();

        if ($userIds->isEmpty()) {
            return redirect()
                ->route('league-players.index', $league)
                ->with('success', 'All players already have a role.');
        }

        $updated = User::whereIn('id', $userIds)->update(['position_id' => $position->id]);

        return redirect()
            ->route('league-players.index', $league)
            ->with('success', "Assigned {$position->name} role to {$updated} player(s).");
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

        // Validate position selection (optional - use default if not provided)
        $positionId = $request->position_id;
        
        // If no position provided, get the first available position for the league's game
        if (!$positionId) {
            $defaultPosition = \App\Models\GamePosition::where('game_id', $league->game_id)->first();
            if ($defaultPosition) {
                $positionId = $defaultPosition->id;
            }
        } else {
            // Validate the provided position exists and belongs to the league's game
            $request->validate([
                'position_id' => 'exists:game_positions,id'
            ]);
        }

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
        if ($user->position_id != $positionId) {
            $user->update(['position_id' => $positionId]);
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

    /**
     * Simple player registration method
     */
    public function simpleRegister($leagueId)
    {
        $league = League::find($leagueId);
        if (!$league) {
            return response()->json(['success' => false, 'message' => 'League not found'], 404);
        }
        
        $user = auth()->user();
        if (!$user || !$user->isPlayer()) {
            return response()->json(['success' => false, 'message' => 'Only players can register'], 403);
        }
        
        // Check if already registered
        $existing = LeaguePlayer::where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->first();
        
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Already registered'], 400);
        }
        
        // Get default position
        $defaultPosition = \App\Models\GamePosition::where('game_id', $league->game_id)->first();
        
        // Create registration
        LeaguePlayer::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'base_price' => $league->player_reg_fee ?? 0,
            'retention' => false,
            'created_by' => $user->id,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Registration successful']);
    }

    /**
     * Toggle retention status for a player.
     */
    public function toggleRetention(League $league, LeaguePlayer $leaguePlayer): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is an organizer for this league, admin, or team owner of the player's team
        $isAuthorized = $user->isOrganizerForLeague($league->id) || 
                       $user->isAdmin() || 
                       ($leaguePlayer->league_team_id && $user->isOwnerOfTeam($leaguePlayer->leagueTeam->team_id));
        
        if (!$isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to modify retention status for this player.'
            ], 403);
        }

        // Check if player is not sold (can be available, pending, unsold, etc.)
        if ($leaguePlayer->status === 'sold') {
            return response()->json([
                'success' => false,
                'message' => 'Sold players cannot be marked as retention players.'
            ], 400);
        }

        // Check retention limit for the team
        $teamRetentionCount = LeaguePlayer::where('league_team_id', $leaguePlayer->league_team_id)
            ->where('retention', true)
            ->count();

        if (!$leaguePlayer->retention && $teamRetentionCount >= $league->retention_players) {
            return response()->json([
                'success' => false,
                'message' => 'This team has reached the maximum retention player limit (' . $league->retention_players . ').'
            ], 400);
        }

        try {
            $leaguePlayer->update(['retention' => !$leaguePlayer->retention]);

            return response()->json([
                'success' => true,
                'message' => $leaguePlayer->retention ? 'Player marked as retention player.' : 'Player unmarked as retention player.',
                'retention' => $leaguePlayer->retention
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update retention status. Please try again.'
            ], 500);
        }
    }

    /**
     * Add a player as retention to a team.
     */
    public function addRetentionPlayer(Request $request, League $league): JsonResponse
    {
        $user = Auth::user();
        
        // Validate request
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
            'league_team_id' => 'required|exists:league_teams,id',
        ]);

        $leaguePlayer = LeaguePlayer::find($request->league_player_id);
        $leagueTeam = LeagueTeam::find($request->league_team_id);

        // Verify the player and team belong to this league
        if ($leaguePlayer->league_id !== $league->id || $leagueTeam->league_id !== $league->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid player or team for this league.'
            ], 400);
        }

        // Check authorization
        $isAuthorized = $user->isOrganizerForLeague($league->id) || 
                       $user->isAdmin() || 
                       $user->isOwnerOfTeam($leagueTeam->team_id);
        
        if (!$isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to add retention players to this team.'
            ], 403);
        }

        // Check if player is not sold and not already retention
        if ($leaguePlayer->status === 'sold') {
            return response()->json([
                'success' => false,
                'message' => 'Sold players cannot be added as retention players.'
            ], 400);
        }

        if ($leaguePlayer->retention) {
            return response()->json([
                'success' => false,
                'message' => 'This player is already marked as a retention player.'
            ], 400);
        }

        // Check if player is already assigned to a different team (only if they have a team assigned)
        if ($leaguePlayer->league_team_id !== null && $leaguePlayer->league_team_id !== $leagueTeam->id) {
            return response()->json([
                'success' => false,
                'message' => 'This player is already assigned to a different team.'
            ], 400);
        }

        // Check retention limit for the team
        $teamRetentionCount = LeaguePlayer::where('league_team_id', $leagueTeam->id)
            ->where('retention', true)
            ->count();

        if ($teamRetentionCount >= $league->retention_players) {
            return response()->json([
                'success' => false,
                'message' => 'This team has reached the maximum retention player limit (' . $league->retention_players . ').'
            ], 400);
        }

        try {
            // Update player to assign to team and mark as retention
            $updateData = ['retention' => true];
            if ($leaguePlayer->league_team_id === null) {
                $updateData['league_team_id'] = $leagueTeam->id;
            }
            
            \Log::info('Updating player with data:', $updateData);
            \Log::info('Player before update:', [
                'id' => $leaguePlayer->id,
                'name' => $leaguePlayer->player->name,
                'league_team_id' => $leaguePlayer->league_team_id,
                'retention' => $leaguePlayer->retention
            ]);
            
            $leaguePlayer->update($updateData);
            
            // Refresh the player to get updated data
            $leaguePlayer->refresh();
            
            \Log::info('Player after update:', [
                'id' => $leaguePlayer->id,
                'name' => $leaguePlayer->player->name,
                'league_team_id' => $leaguePlayer->league_team_id,
                'retention' => $leaguePlayer->retention
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Player successfully added as retention player.',
                'player' => [
                    'id' => $leaguePlayer->id,
                    'name' => $leaguePlayer->player->name,
                    'position' => $leaguePlayer->player->position->name,
                    'retention' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add retention player. Please try again.'
            ], 500);
        }
    }
}
