<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeaguePlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaguePlayerController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $query = LeaguePlayer::with(['user', 'league', 'leagueTeam.team']);

        if ($request->filled('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('retention')) {
            $query->where('retention', $request->retention === 'true');
        }

        $leaguePlayers = $query->orderBy('created_at', 'desc')->paginate(50)->appends($request->query());
        $leagues = League::orderBy('name')->get();

        return view('admin.league-players.index', compact('leaguePlayers', 'leagues'));
    }
}
