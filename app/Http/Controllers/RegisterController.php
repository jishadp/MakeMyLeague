<?php

namespace App\Http\Controllers;

use App\Models\GamePosition;
use App\Models\LocalBody;
use App\Models\User;
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
        $roles = GamePosition::all();
        $localBodies = LocalBody::all();
        return view('register', compact('roles', 'localBodies'));
    }

    /**
     * Handle the registration request.
     */
    public function register(RegisterRequest $request)
    {
        // dd($request->validated());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'pin' => bcrypt($request->pin),
            'position_id' => $request->position_id,
            'local_body_id' => $request->local_body_id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }
}
