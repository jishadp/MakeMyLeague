<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaguePlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardApiController extends Controller
{
    public function index()
    {
        // ============ GLOBAL PLAYER SPOTLIGHT (RANDOM SOLD PLAYERS) ============
        $spotlight = LeaguePlayer::where('status', 'sold')
            ->with([
                'user.position',
                'league.game',
                'leagueTeam.team',
            ])
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function ($player) {
                return $this->transformPlayer($player);
            });

        // ============ AUCTION LEADERBOARD ============
        $leaderboard = LeaguePlayer::where('status', 'sold')
            ->with([
                'user.position',
                'league.game',
                'leagueTeam.team'
            ])
            ->orderBy('bid_price', 'desc')
            ->take(5)
            ->get()
            ->map(function ($player, $index) {
                return $this->transformPlayer($player, $index + 1);
            });

        return response()->json([
            'spotlight' => $spotlight,
            'leaderboard' => $leaderboard,
        ]);
    }

    private function transformPlayer($player, $rank = null)
    {
        $photoUrl = $player->user->photo ? url(Storage::url($player->user->photo)) : null;

        return [
            'id' => $player->id,
            'name' => $player->user->name,
            'photo' => $photoUrl,
            'price' => $player->bid_price,
            'role' => $player->user->position->name ?? 'Player',
            'team_name' => $player->leagueTeam->team->name ?? 'N/A',
            'league_name' => $player->league->name ?? 'N/A',
            'rank' => $rank,
        ];
    }
}
