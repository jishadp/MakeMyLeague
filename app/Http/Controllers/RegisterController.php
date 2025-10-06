<?php

namespace App\Http\Controllers;

use App\Models\GamePosition;
use App\Models\LocalBody;
use App\Models\District;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Role;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        $districts = District::with('state')->orderBy('name')->get();
        $localBodies = LocalBody::with('district')->orderBy('name')->get();
        return view('register', compact('districts', 'localBodies'));
    }

    /**
     * Handle the registration request.
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'country_code' => $request->country_code,
            'pin' => bcrypt($request->pin),
            'local_body_id' => $request->local_body_id,
        ]);

        // Automatically assign Player role to new users
        $playerRole = Role::where('name', User::ROLE_PLAYER)->first();
        if ($playerRole) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $playerRole->id,
            ]);
        }

        Auth::login($user);

        // Check if user was trying to join a league before registration
        if (session()->has('join_league_after_registration')) {
            $leagueSlug = session()->pull('join_league_after_registration');
            $league = \App\Models\League::where('slug', $leagueSlug)->first();
            
            if ($league) {
                return redirect()->route('leagues.join-link', $league)
                    ->with('success', 'Registration successful! You can now join the league.');
            }
        }

        // Redirect to dashboard after successful registration
        return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to MakeMyLeague.');
    }

    /**
     * Get local bodies by district ID.
     */
    public function getLocalBodiesByDistrict(Request $request)
    {
        $districtId = $request->get('district_id');
        
        if (!$districtId) {
            return response()->json([]);
        }
        
        $localBodies = LocalBody::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($localBodies);
    }
}
