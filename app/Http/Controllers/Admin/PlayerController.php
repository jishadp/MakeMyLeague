<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GamePosition;
use App\Models\LocalBody;
use App\Models\League;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PlayerController extends Controller
{
    /**
     * Display a listing of the players for admin management.
     */
    public function index(Request $request): View
    {
        $players = $this->buildPlayerQuery($request)
            ->paginate(15)
            ->withQueryString();

        // Get all roles for filtering
        $positions = GamePosition::orderBy('name')->get();

        $localBodies = LocalBody::orderBy('name')->get();
        $leagues = League::orderBy('name')->get();

        return view('admin.players.index', compact('players', 'positions', 'localBodies', 'leagues'));
    }

    /**
     * Export player listing in the requested format.
     */
    public function export(Request $request, string $format)
    {
        $format = strtolower($format);

        if (!in_array($format, ['json', 'csv', 'pdf'])) {
            abort(404);
        }

        $players = $this->buildPlayerQuery($request)->get();
        $playerData = $players->map(function (User $player) {
            $leagueNames = $player->leaguePlayers
                ->filter(fn ($leaguePlayer) => $leaguePlayer->league)
                ->pluck('league.name')
                ->unique()
                ->values();

            return [
                'id' => $player->id,
                'name' => $player->name,
                'mobile' => $player->mobile,
                'email' => $player->email,
                'position' => optional($player->position)->name,
                'local_body' => optional($player->localBody)->name,
                'joined_at' => optional($player->created_at)?->format('Y-m-d H:i:s'),
                'leagues' => $leagueNames->implode(', '),
            ];
        });

        $filename = 'players-export-' . now()->format('Ymd-His');

        if ($format === 'json') {
            return response()
                ->json($playerData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');
        }

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ];

            $callback = function () use ($playerData) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['ID', 'Name', 'Mobile', 'Email', 'Position', 'Local Body', 'Joined At', 'Leagues']);

                foreach ($playerData as $player) {
                    fputcsv($handle, [
                        $player['id'],
                        $player['name'],
                        $player['mobile'],
                        $player['email'],
                        $player['position'],
                        $player['local_body'],
                        $player['joined_at'],
                        $player['leagues'],
                    ]);
                }

                fclose($handle);
            };

            return response()->streamDownload($callback, $filename . '.csv', $headers);
        }

        $pdf = \PDF::loadView('admin.players.export-pdf', [
            'players' => $players,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename . '.pdf');
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

    /**
     * Update player photo.
     */
    public function updatePhoto(Request $request, User $player)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240'
            ]);

            // Delete old photo if exists
            if ($player->photo && Storage::disk('public')->exists($player->photo)) {
                Storage::disk('public')->delete($player->photo);
            }
            
            // Process and save the image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('photo'));
            $image->resize(300, 300);
            $encoded = $image->toJpeg(85);
            
            $filename = 'profile-photos/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $encoded);
            
            // Update the player's photo
            $player->update([
                'photo' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo_url' => Storage::url($filename)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build the common player query with filters applied.
     */
    protected function buildPlayerQuery(Request $request): Builder
    {
        $query = User::query()
            ->with(['position', 'localBody', 'leaguePlayers.league']);

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('local_body_id')) {
            $query->where('local_body_id', $request->local_body_id);
        }

        if ($request->filled('league_id')) {
            $query->whereHas('leaguePlayers', function (Builder $leagueQuery) use ($request) {
                $leagueQuery->where('league_id', $request->league_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort_by')) {
            $sortDirection = $request->filled('sort_dir') && $request->sort_dir === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query;
    }
}
