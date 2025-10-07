<?php

namespace App\Http\Controllers;

use App\Models\GamePosition;
use App\Models\User;
use App\Models\LocalBody;
use App\Models\District;
use App\Models\League;
use App\Models\LeaguePlayer;
use App\Http\Requests\StorePlayerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\View\View;

class PlayerController extends Controller
{
    /**
     * Display a listing of the players.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->with(['position', 'localBody'])
            ->whereHas('roles', function($q) {
                $q->where('name', 'Player');
            });

        // Filter by role
        if ($request->has('position_id') && $request->position_id != '') {
            $query->where('position_id', $request->position_id);
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
        $positions = GamePosition::orderBy('name')->get();

        // Get all local bodies for filtering
        $localBodies = LocalBody::orderBy('name')->get();

        return view('players.index', compact('players', 'positions', 'localBodies'));
    }

    /**
     * Show the form for creating a new player.
     */
    public function create()
    {
        // Check if the current user has admin privileges
        if (!Auth::user() || !Auth::user()->isOrganizer()) {
            return redirect()->route('players.index')
                ->with('error', 'You do not have permission to create players.');
        }

        $positions = GamePosition::orderBy('name')->get();
        $districts = District::with('state')->orderBy('name')->get();
        $localBodies = LocalBody::with('district')->get();
        return view('players.create', compact('positions', 'districts', 'localBodies'));
    }

    /**
     * Store a newly created player in storage.
     */
    public function store(StorePlayerRequest $request)
    {
        $validated = $request->validated();

        $player = User::create([
            'name' => $validated['name'],
            'country_code' => $validated['country_code'],
            'mobile' => $validated['mobile'],
            'pin' => bcrypt($validated['pin']),
            'email' => $validated['email'] ?? null,
            'position_id' => $validated['position_id'],
            'local_body_id' => $validated['local_body_id'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('photo'));
            $image->resize(300, 300);
            $encoded = $image->toJpeg(85);
            
            $filename = 'profile-photos/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $encoded);
            $player->photo = $filename;
            $player->save();
        }

        // Check if there's a league context and add player to league
        if ($request->has('league_slug')) {
            $league = League::where('slug', $request->league_slug)->firstOrFail();

            // Check if player is not already in this league
            $existingPlayer = LeaguePlayer::where('user_id', $player->id)
                ->where('league_id', $league->id)
                ->first();

            if (!$existingPlayer) {
                LeaguePlayer::create([
                    'user_id' => $player->id,
                    'league_id' => $league->id,
                    'status' => 'available',
                    'base_price' => $league->player_reg_fee,
                    'retention' => false,
                ]);

                return redirect()->route('league-players.index', $league)
                    ->with('success', 'Player created and added to league successfully!');
            }
        }

        return redirect()->route('players.show', $player)
            ->with('success', 'Player created successfully!');
    }

    public function register(Request $request, League $league)
    {
        // Check if already registered
        $existing = LeaguePlayer::where('user_id', auth()->id())
            ->where('league_id', $league->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'You are already registered in this league.');
        }

        LeaguePlayer::create([
            'user_id' => auth()->id(),
            'league_id' => $league->id,
            'status' => 'available',
            'base_price' => $league->player_reg_fee,
            'retention' => false,
        ]);

        return redirect()->route('league-players.index', $league)
            ->with('success', 'You have registered for this league!');
    }




    /**
     * Display the specified player.
     */
    public function show(User $player): View
    {
        $player->load([
            'position', 
            'localBody',
            'leaguePlayers.league',
            'leaguePlayers.leagueTeam',
            'leaguePlayers.auctionBids' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }
        ]);
        
        // Get recent auction data
        $recentAuctions = $player->leaguePlayers()
            ->with(['auctionBids' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(3);
            }, 'league', 'leagueTeam'])
            ->whereHas('auctionBids')
            ->get()
            ->pluck('auctionBids')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(3);
            
        // Get league teams data
        $leagueTeams = $player->leaguePlayers()
            ->with(['league', 'leagueTeam'])
            ->whereNotNull('league_team_id')
            ->get();
            
        // Get registered leagues with active and pending status
        $registeredLeagues = $player->leaguePlayers()
            ->with(['league.game', 'league.localBody.district'])
            ->whereIn('status', ['pending', 'available', 'sold', 'active'])
            ->whereHas('league', function($query) {
                $query->whereIn('status', ['active', 'pending']);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('players.show', compact('player', 'recentAuctions', 'leagueTeams', 'registeredLeagues'));
    }

    /**
     * Show the form for editing the specified player.
     */
    public function edit(User $player)
    {
        // Only allow admin or the player themselves to edit
        if (!Auth::user() || (Auth::id() !== $player->id && !Auth::user()->isOrganizer())) {
            return redirect()->route('players.show', $player)
                ->with('error', 'You do not have permission to edit this player.');
        }

        $roles = GamePosition::orderBy('name')->get();
        $localBodies = LocalBody::with('district')->get();
        return view('players.edit', compact('player', 'roles', 'localBodies'));
    }

    /**
     * Update the specified player in storage.
     */
    public function update(Request $request, User $player)
    {
        // Only allow admin or the player themselves to update
        if (!Auth::user() || (Auth::id() !== $player->id && !Auth::user()->isOrganizer())) {
            return redirect()->route('players.show', $player)
                ->with('error', 'You do not have permission to update this player.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'pin' => 'required|string|max:10',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $player->id,
            'local_body_id' => 'nullable|exists:local_bodies,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Only admin can change role
        if (Auth::user()->isOrganizer()) {
            $rules['position_id'] = 'required|exists:game_positions,id';
        }

        $validated = $request->validate($rules);

        $player->name = $validated['name'];
        $player->mobile = $validated['mobile'];
        $player->pin = $validated['pin'];
        $player->email = $validated['email'] ?? null;
        $player->local_body_id = $validated['local_body_id'] ?? null;

        // Only admin can change role
        if (Auth::user()->isOrganizer() && isset($validated['position_id'])) {
            $player->position_id = $validated['position_id'];
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($player->photo && Storage::disk('public')->exists($player->photo)) {
                Storage::disk('public')->delete($player->photo);
            }
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('photo'));
            $image->resize(300, 300);
            $encoded = $image->toJpeg(85);
            
            $filename = 'profile-photos/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $encoded);
            $player->photo = $filename;
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
        if (!Auth::user() || !Auth::user()->isOrganizer()) {
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
