<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeaguePlayerCategory;
use App\Models\LocalBody;
use App\Models\District; // Assuming District model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaguePlayerCategoryController extends Controller
{
    public function index(League $league)
    {
        $categories = $league->playerCategories()->withCount('players')->get();
        // Assuming District model is available. If not, use query builder or relationship through User/LocalBody if needed. 
        // For now assuming District model exists or passing empty if not crucial for index (it is needed for auto assign modal).
        // Check AuctionController usage of $districts.
        $districts = \App\Models\District::orderBy('name')->get(); 

        return view('leagues.categories.index', compact('league', 'categories', 'districts'));
    }

    public function store(Request $request, League $league)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_requirement' => 'required|integer|min:0',
            'max_requirement' => 'nullable|integer|min:0',
        ]);

        $league->playerCategories()->create($request->all());

        return response()->json(['success' => true, 'message' => 'Category created successfully']);
    }

    public function destroy(League $league, LeaguePlayerCategory $category)
    {
        if ($category->league_id !== $league->id) {
            abort(403);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }

    public function autoAssign(Request $request, League $league)
    {
        $request->validate([
            'category_id' => 'required|exists:league_player_categories,id',
            'district_id' => 'nullable|exists:districts,id',
            'local_body_id' => 'nullable|exists:local_bodies,id',
        ]);

        if (!$request->district_id && !$request->local_body_id) {
            return response()->json(['success' => false, 'message' => 'Please select a district or local body']);
        }

        $query = $league->leaguePlayers()
            ->whereHas('user', function ($q) use ($request) {
                if ($request->local_body_id) {
                    $q->where('local_body_id', $request->local_body_id);
                } elseif ($request->district_id) {
                    $q->whereHas('localBody', function ($q2) use ($request) {
                        $q2->where('district_id', $request->district_id);
                    });
                }
            });

        $count = $query->count();
        $query->update(['league_player_category_id' => $request->category_id]);

        return response()->json(['success' => true, 'message' => "$count players assigned to category successfully"]);
    }

    public function assignPlayer(Request $request, League $league)
    {
        $request->validate([
            'league_player_id' => 'required|exists:league_players,id',
            'category_id' => 'nullable|exists:league_player_categories,id',
        ]);

        $leaguePlayer = LeaguePlayer::where('league_id', $league->id)
            ->where('id', $request->league_player_id)
            ->firstOrFail();

        $leaguePlayer->league_player_category_id = $request->category_id;
        $leaguePlayer->save();

        return response()->json(['success' => true, 'message' => 'Player category updated successfully']);
    }

    public function searchLocalBodies(Request $request)
    {
        $query = $request->get('query');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $lbs = LocalBody::where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'district_id']);

        return response()->json($lbs);
    }
    public function getPlayers(Request $request, League $league)
    {
        $query = $league->leaguePlayers()->with(['player', 'category']);

        if ($request->has('category_id')) {
            if ($request->category_id === 'uncategorized' || $request->category_id === 'null') {
                $query->whereNull('league_player_category_id');
            } elseif ($request->category_id !== 'all' && $request->category_id !== '') {
                $query->where('league_player_category_id', $request->category_id);
            }
        }

        if ($request->search) {
             $query->whereHas('player', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $players = $query->paginate(20);

        return response()->json($players);
    }

    public function searchLeaguePlayers(Request $request, League $league)
    {
        $query = $league->leaguePlayers()->with('player');

        if ($request->search) {
             $query->whereHas('player', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $players = $query->take(10)->get()->map(function($lp) {
            return [
                'id' => $lp->id,
                'name' => $lp->player->name,
                'category_name' => $lp->category ? $lp->category->name : 'Uncategorized'
            ];
        });

        return response()->json($players);
    }

    public function bulkAssign(Request $request, League $league)
    {
        $request->validate([
            'player_ids' => 'required|array|min:1',
            'player_ids.*' => 'exists:league_players,id',
            'category_id' => 'nullable|exists:league_player_categories,id',
        ]);

        // Verify all players belong to this league
        $count = LeaguePlayer::where('league_id', $league->id)
            ->whereIn('id', $request->player_ids)
            ->update(['league_player_category_id' => $request->category_id]);

        $categoryName = $request->category_id 
            ? LeaguePlayerCategory::find($request->category_id)->name 
            : 'Uncategorized';

        return response()->json([
            'success' => true, 
            'message' => "{$count} players assigned to {$categoryName} successfully"
        ]);
    }
}
