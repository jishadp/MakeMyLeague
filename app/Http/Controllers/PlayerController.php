<?php

namespace App\Http\Controllers;

use App\Models\GameRole;
use App\Models\User;
use App\Models\LocalBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        
        // Filter by local body
        if ($request->has('local_body_id') && $request->local_body_id != '') {
            $query->where('local_body_id', $request->local_body_id);
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
        
        // Get all local bodies for filtering
        $localBodies = LocalBody::orderBy('name')->get();
        
        return view('players.index', compact('players', 'roles', 'localBodies'));
    }

    /**
     * Show the form for creating a new player.
     */
    public function create()
    {
        // Check if the current user has admin privileges
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('players.index')
                ->with('error', 'You do not have permission to create players.');
        }
        
        $roles = GameRole::orderBy('name')->get();
        $localBodies = LocalBody::with('district')->get();
        return view('players.create', compact('roles', 'localBodies'));
    }

    /**
     * Store a newly created player in storage.
     */
    public function store(Request $request)
    {
        // Check if the current user has admin privileges
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('players.index')
                ->with('error', 'You do not have permission to create players.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|max:15',
            'pin' => 'nullable|string|max:10',
            'role_id' => 'required|exists:game_roles,id',
            'local_body_id' => 'required|exists:local_bodies,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $player = new User();
        $player->name = $validated['name'];
        $player->email = $validated['email'];
        $player->password = Hash::make($validated['password']);
        $player->mobile = $validated['mobile'];
        $player->pin = $validated['pin'] ?? null;
        $player->role_id = $validated['role_id'];
        $player->local_body_id = $validated['local_body_id'];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('player-photos', 'public');
            $player->photo = 'storage/' . $photoPath;
        }

        $player->save();

        return redirect()->route('players.show', $player)
            ->with('success', 'Player created successfully!');
    }

    /**
     * Display the specified player.
     */
    public function show(User $player): View
    {
        $player->load(['role', 'localBody']);
        return view('players.show', compact('player'));
    }

    /**
     * Show the form for editing the specified player.
     */
    public function edit(User $player)
    {
        // Only allow admin or the player themselves to edit
        if (!Auth::user() || (Auth::id() !== $player->id && !Auth::user()->isAdmin())) {
            return redirect()->route('players.show', $player)
                ->with('error', 'You do not have permission to edit this player.');
        }

        $roles = GameRole::orderBy('name')->get();
        $localBodies = LocalBody::with('district')->get();
        return view('players.edit', compact('player', 'roles', 'localBodies'));
    }

    /**
     * Update the specified player in storage.
     */
    public function update(Request $request, User $player)
    {
        // Only allow admin or the player themselves to update
        if (!Auth::user() || (Auth::id() !== $player->id && !Auth::user()->isAdmin())) {
            return redirect()->route('players.show', $player)
                ->with('error', 'You do not have permission to update this player.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $player->id,
            'mobile' => 'required|string|max:15',
            'pin' => 'nullable|string|max:10',
            'local_body_id' => 'required|exists:local_bodies,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Only admin can change role
        if (Auth::user()->isAdmin()) {
            $rules['role_id'] = 'required|exists:game_roles,id';
        }

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $player->name = $validated['name'];
        $player->email = $validated['email'];
        $player->mobile = $validated['mobile'];
        $player->pin = $validated['pin'] ?? null;
        $player->local_body_id = $validated['local_body_id'];
        
        // Only admin can change role
        if (Auth::user()->isAdmin() && isset($validated['role_id'])) {
            $player->role_id = $validated['role_id'];
        }

        // Update password if provided
        if ($request->filled('password')) {
            $player->password = Hash::make($validated['password']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('player-photos', 'public');
            $player->photo = 'storage/' . $photoPath;
        }

        $player->save();

        return redirect()->route('players.show', $player)
            ->with('success', 'Player updated successfully!');
    }

    /**
     * Remove the specified player from storage.
     */
    public function destroy(User $player)
    {
        // Only allow admin to delete
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('players.show', $player)
                ->with('error', 'You do not have permission to delete this player.');
        }

        // Check if the player is associated with any leagues
        if ($player->leaguePlayers()->exists()) {
            return redirect()->route('players.show', $player)
                ->with('error', 'Cannot delete player. They are associated with one or more leagues.');
        }

        $player->delete();

        return redirect()->route('players.index')
            ->with('success', 'Player deleted successfully!');
    }
}
