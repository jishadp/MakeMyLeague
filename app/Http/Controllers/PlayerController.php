<?php

namespace App\Http\Controllers;

use App\Models\GameRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    /**
     * Display a listing of the players.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->with(['role', 'localBody'])
            ->players();
        
        // Filter by role
        if ($request->has('role_id') && $request->role_id != '') {
            $query->where('role_id', $request->role_id);
        }
        
        // Sort by name
        if ($request->has('sort_by') && $request->sort_by != '') {
            $sortDirection = $request->has('sort_dir') && $request->sort_dir == 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $players = $query->paginate(12)->withQueryString();
        
        // Get all roles for filtering
        $roles = GameRole::orderBy('name')->get();
        
        return view('players.index', compact('players', 'roles'));
    }

    /**
     * Display the specified player.
     */
    public function show(User $player): View
    {
        $player->load(['role', 'localBody']);
        return view('players.show', compact('player'));
    }
}
