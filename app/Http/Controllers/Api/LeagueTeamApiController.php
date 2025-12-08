<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeagueTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeagueTeamApiController extends Controller
{
    public function index(Request $request, League $league)
    {
        $leagueTeams = $league->leagueTeams()
            ->with(['team.owners', 'leaguePlayers.user'])
            ->get()
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'data' => $leagueTeams->map(fn ($lt) => $this->transformLeagueTeam($lt, $league))
        ]);
    }

    protected function transformLeagueTeam(LeagueTeam $leagueTeam, League $league): array
    {
        $players = $leagueTeam->leaguePlayers->sortByDesc(function ($player) {
            $value = (int) ($player->bid_price ?? $player->base_price ?? 0);
            return sprintf('%d-%012d', $player->retention ? 1 : 0, $value);
        })->values();

        $playerSpend = $players->sum(fn($p) => $p->bid_price ?? $p->base_price ?? 0);
        $remainingWallet = $leagueTeam->wallet_balance;
        $totalSpent = $league->team_wallet_limit
            ? max(0, ($league->team_wallet_limit - $remainingWallet))
            : $playerSpend;

        return [
            'id' => $leagueTeam->id,
            'slug' => $leagueTeam->slug,
            'team' => [
                'id' => $leagueTeam->team->id,
                'name' => $leagueTeam->team->name,
                'slug' => $leagueTeam->team->slug,
                'logo' => $leagueTeam->team->logo ? url(Storage::url($leagueTeam->team->logo)) : null,
                'banner' => $leagueTeam->team->banner ? url(Storage::url($leagueTeam->team->banner)) : null,
                'owner_name' => $leagueTeam->team->owners->first()?->name ?? 'No owner',
            ],
            'status' => $leagueTeam->status,
            'players_count' => $players->count(),
            'wallet' => [
                'remaining' => $remainingWallet,
                'spent' => $totalSpent,
                'retained_count' => $players->where('retention', true)->count(),
            ],
            'squad' => $players->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->user->name ?? 'Unknown',
                'photo' => $p->user->profile_photo_path ? url(Storage::url($p->user->profile_photo_path)) : null,
                'price' => $p->bid_price ?? $p->base_price ?? 0,
                'is_retained' => (bool) $p->retention,
                'position' => $p->user->position->name ?? null,
                'current_club' => $p->leagueTeam ? $p->leagueTeam->team->name : null,
            ]),
        ];
    }
}
