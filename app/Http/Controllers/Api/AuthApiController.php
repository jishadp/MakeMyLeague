<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'pin'    => 'required',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (! $user || ! Hash::check($request->pin, $user->pin)) {
            throw ValidationException::withMessages([
                'mobile' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'required|string|unique:users,mobile',
            'pin'    => 'required|string|min:4',
        ]);

        $user = User::create([
            'name'   => $request->name,
            'mobile' => $request->mobile,
            'pin'    => Hash::make($request->pin),
            'email'  => $request->mobile . '@makemyleague.app', // Placeholder email
            // Add default role or other required fields here if necessary
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
