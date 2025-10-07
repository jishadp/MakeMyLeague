<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GamePosition;
use App\Models\LocalBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PlayerController extends Controller
{
    /**
     * Display a listing of the players for admin management.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->with(['position', 'localBody']);

        // Filter by role
        if ($request->has('position_id') && $request->position_id != '') {
            $query->where('position_id', $request->position_id);
        }

        // Filter by local body
        if ($request->has('local_body_id') && $request->local_body_id != '') {
            $query->where('local_body_id', $request->local_body_id);
        }

        // Search by name or mobile
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Sort by name
        if ($request->has('sort_by') && $request->sort_by != '') {
            $sortDirection = $request->has('sort_dir') && $request->sort_dir == 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $players = $query->paginate(15)->withQueryString();

        // Get all roles for filtering
        $positions = GamePosition::orderBy('name')->get();

        // Get all local bodies for filtering
        $localBodies = LocalBody::orderBy('name')->get();

        return view('admin.players.index', compact('players', 'positions', 'localBodies'));
    }

    /**
     * Reset a player's PIN to a default value.
     */
    public function resetPin(User $player)
    {
        try {
            // Generate a random 4-digit PIN
            $newPin = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            // Update the player's PIN
            $player->update([
                'pin' => Hash::make($newPin)
            ]);

            return response()->json([
                'success' => true,
                'message' => "Player PIN has been reset successfully.",
                'new_pin' => $newPin,
                'player_name' => $player->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting PIN: ' . $e->getMessage()
            ], 500);
        }
    }
}
