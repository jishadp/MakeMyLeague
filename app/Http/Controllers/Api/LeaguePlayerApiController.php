<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeaguePlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaguePlayerApiController extends Controller
{
    public function index(Request $request, League $league)
    {
        $query = $league->leaguePlayers()->with(['user.localBody', 'user.position']);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $players = $query->get()->sortByDesc(function ($player) {
            $value = (int) ($player->bid_price ?? $player->base_price ?? 0);
            return sprintf('%d-%012d', $player->retention ? 1 : 0, $value);
        })->values();

        // Calculate counts
        $allPlayers = $league->leaguePlayers; // Get all for counts, irrespective of filters
        $counts = [
            'total' => $allPlayers->count(),
            'retained' => $allPlayers->where('retention', true)->count(),
            'sold' => $allPlayers->where('status', 'sold')->count(),
            'available' => $allPlayers->where('status', 'available')->count(),
            'unsold' => $allPlayers->where('status', 'unsold')->count(),
        ];

        return response()->json([
            'counts' => $counts,
            'data' => $players->map(fn ($p) => $this->transformLeaguePlayer($p))
        ]);
    }

    protected function transformLeaguePlayer(LeaguePlayer $player): array
    {
        return [
            'id' => $player->id,
            'slug' => $player->slug,
            'name' => $player->user->name ?? 'Unknown',
            'photo' => $player->user->photo ? url(Storage::url($player->user->photo)) : null,
            'position' => $player->user->position->name ?? 'Role',
            'local_body' => $player->user->localBody->name ?? 'Unknown',
            'status' => $player->status,
            'price' => $player->bid_price ?? $player->base_price ?? 0,
            'is_retained' => (bool) $player->retention,
        ];
    }
}
