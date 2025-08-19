<?php

namespace App\Http\Controllers;

use App\Models\GameRole;
use App\Models\LocalBody;
use App\Models\User;
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
        $roles = GameRole::all();
        $localBodies = LocalBody::all();
        return view('register', compact('roles', 'localBodies'));
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:100|unique:users',
            'mobile' => 'required|string|max:15|unique:users',
            'pin' => 'required|string|min:4|max:6',
            'role_id' => 'nullable|exists:game_roles,id',
            'local_body_id' => 'nullable|exists:local_bodies,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'pin' => bcrypt($request->pin),
            'role_id' => $request->role_id,
            'local_body_id' => $request->local_body_id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }
}
