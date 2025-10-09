<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['position.game', 'localBody.district', 'gameRoles.game', 'gameRoles.gamePosition']);
        $games = \App\Models\Game::with('roles')->where('active', true)->get();
        $localBodies = \App\Models\LocalBody::with('district')->get();
        return view('profile.show', compact('user', 'games', 'localBodies'));
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($request->hasFile('photo') && $request->ajax()) {
                $request->validate([
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240'
                ]);
            } else {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
                    'mobile' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
                    'country_code' => 'required|string',
                    'game_roles' => 'nullable|array',
                    'game_roles.*' => 'nullable|array',
                    'game_roles.*.position_id' => 'nullable|exists:game_positions,id',
                    'primary_game_id' => 'nullable|exists:games,id',
                    'local_body_id' => 'nullable|exists:local_bodies,id',
                    'pin' => 'nullable|string|min:4|max:6'
                ]);
            }

            $data = [];
            
            // Handle game roles for both AJAX and non-AJAX requests
            if ($request->has('game_roles')) {
                // Delete existing game roles
                $user->gameRoles()->delete();
                
                $hasGameRoles = false;
                
                // Create new game roles
                foreach ($request->game_roles as $gameId => $gameRole) {
                    if (isset($gameRole['position_id']) && $gameRole['position_id']) {
                        $user->gameRoles()->create([
                            'game_id' => $gameId,
                            'game_position_id' => $gameRole['position_id'],
                            'is_primary' => $request->primary_game_id == $gameId
                        ]);
                        $hasGameRoles = true;
                    }
                }
                
                // Auto-assign Player role if user has game roles and doesn't have Player role yet
                if ($hasGameRoles && !$user->isPlayer()) {
                    $playerRole = \App\Models\Role::where('name', \App\Models\User::ROLE_PLAYER)->first();
                    if ($playerRole && !$user->roles->contains($playerRole->id)) {
                        $user->roles()->attach($playerRole->id);
                    }
                }
            }
            
            if (!$request->ajax()) {
                $data = $request->only(['name', 'mobile', 'country_code', 'local_body_id']);
                
                // Only update email if it's provided and not empty
                if ($request->filled('email')) {
                    $data['email'] = $request->email;
                }
            }
            
            $pinChanged = false;
            if ($request->filled('pin')) {
                $data['pin'] = bcrypt($request->pin);
                $pinChanged = true;
            }

            if ($request->hasFile('photo')) {
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }
                
                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('photo'));
                $image->resize(300, 300);
                $encoded = $image->toJpeg(85);
                
                $filename = 'profile-photos/' . uniqid() . '.jpg';
                Storage::disk('public')->put($filename, $encoded);
                $data['photo'] = $filename;
            }

            if (!empty($data)) {
                $user->update($data);
            }
            
            if ($pinChanged) {
                Auth::logout();
                return redirect()->route('login')->with('success', 'PIN updated successfully. Please login again.');
            }
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
            }
            
            return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
}