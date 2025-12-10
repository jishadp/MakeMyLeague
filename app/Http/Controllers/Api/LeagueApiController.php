<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeagueApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Base eager loads/counts to mirror the web app data shown in My Leagues.
        $baseWith = [
            'game',
            'localBody',
            'winnerTeam.team',
            'runnerTeam.team',
        ];

        $baseCounts = [
            'leagueTeams as teams_count',
            'leaguePlayers as players_count',
        ];

        if ($user->isAdmin()) {
            $organizedLeagues = League::with($baseWith)->withCount($baseCounts)->get();
        } else {
            $organizedLeagues = $user->organizedLeagues()->with($baseWith)->withCount($baseCounts)->get();
        }

        $playingLeagues = $user->leaguePlayers()
            ->whereIn('status', ['sold', 'available'])
            ->with(['league' => fn ($q) => $q->with($baseWith)->withCount($baseCounts)])
            ->get()
            ->pluck('league')
            ->unique('id')
            ->values();

        $requestedLeagues = $user->leaguePlayers()
            ->where('status', 'pending')
            ->with(['league' => fn ($q) => $q->with($baseWith)->withCount($baseCounts)])
            ->get()
            ->pluck('league')
            ->unique('id')
            ->values();

        $teamOwnerLeagues = $user->isTeamOwner()
            ? League::whereHas('leagueTeams.team', function ($query) use ($user) {
                $query->whereHas('owners', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('role', 'owner');
                });
            })
                ->with($baseWith)
                ->withCount($baseCounts)
                ->get()
            : collect();

        $auctioneerLeagues = League::whereHas('leagueTeams.teamAuctioneer', function ($query) use ($user) {
            $query->where('auctioneer_id', $user->id)->where('status', 'active');
        })
            ->with($baseWith)
            ->withCount($baseCounts)
            ->get();

        return response()->json([
            'organized' => $organizedLeagues->map(fn ($league) => $this->transformLeague($league, 'organizer')),
            'playing' => $playingLeagues->map(fn ($league) => $this->transformLeague($league, 'player')),
            'requested' => $requestedLeagues->map(fn ($league) => $this->transformLeague($league, 'requested')),
            'team_owner' => $teamOwnerLeagues->map(fn ($league) => $this->transformLeague($league, 'team_owner')),
            'auctioneer' => $auctioneerLeagues->map(fn ($league) => $this->transformLeague($league, 'auctioneer')),
        ]);
    }

    protected function transformLeague(League $league, string $context): array
    {
        $winnerTeam = optional($league->winnerTeam)->team;
        $runnerTeam = optional($league->runnerTeam)->team;
        $logoUrl = $league->logo ? url(Storage::url($league->logo)) : null;

        return [
            'id' => $league->id,
            'name' => $league->name,
            'slug' => $league->slug,
            'season' => $league->season,
            'status' => $league->status,
            'game' => [
                'id' => optional($league->game)->id,
                'name' => optional($league->game)->name,
            ],
            'local_body' => [
                'id' => optional($league->localBody)->id,
                'name' => optional($league->localBody)->name,
            ],
            'start_date' => optional($league->start_date)->toDateString(),
            'end_date' => optional($league->end_date)->toDateString(),
            'max_teams' => $league->max_teams,
            'max_team_players' => $league->max_team_players,
            'logo' => $logoUrl,
            'teams_count' => $league->teams_count ?? $league->leagueTeams()->count(),
            'players_count' => $league->players_count ?? $league->leaguePlayers()->count(),
            'team_reg_fee' => $league->team_reg_fee,
            'player_reg_fee' => $league->player_reg_fee,
            'auction_active' => (bool) $league->auction_active,
            'winner_team' => $winnerTeam ? $winnerTeam->only(['id', 'name']) : null,
            'runner_team' => $runnerTeam ? $runnerTeam->only(['id', 'name']) : null,
            'context' => $context,
        ];
    }

    public function show(Request $request, $slug)
    {
        $league = League::where('slug', $slug)->first();
        
        if (!$league) {
            return response()->json([
                'success' => false,
                'message' => 'League not found',
            ], 404);
        }

        $baseWith = [
            'game',
            'localBody',
            'winnerTeam.team',
            'runnerTeam.team',
        ];

        $baseCounts = [
            'leagueTeams as teams_count',
            'leaguePlayers as players_count',
        ];
        
        $league->load($baseWith);
        $league->loadCount($baseCounts);

        $user = $request->user();
        
        // Determine context (organizer, player, etc.) - simplify to 'view' for now or logic check
        // For simplicity, we can use a generic context or check roles.
        // Let's reuse logic from index but for single item.
        $context = 'view';
        if ($league->organizer_id == $user->id) {
            $context = 'organizer';
        } elseif ($user->leaguePlayers()->where('league_id', $league->id)->exists()) {
            $context = 'player';
        }

        return response()->json([
            'success' => true,
            'league' => $this->transformLeague($league, $context),
        ]);
    }
}
