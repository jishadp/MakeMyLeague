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
        return view('register');
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
        ]);

        Auth::login($user);

        // Redirect to role selection instead of dashboard
        return redirect()->route('role-selection.show')->with('success', 'Registration successful! Please select your role.');
    }
}
