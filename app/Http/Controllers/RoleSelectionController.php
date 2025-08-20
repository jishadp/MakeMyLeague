<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSelectionController extends Controller
{
    /**
     * Show the role selection form.
     */
    public function show()
    {
        // Check if user is authenticated and doesn't have a role yet
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user already has a role assigned
        if ($user->roles()->exists()) {
            return redirect()->route('dashboard');
        }

        $roles = Role::orderBy('name')->get();
        return view('role-selection', compact('roles'));
    }

    /**
     * Handle the role selection.
     */
    public function store(Request $request)
    {
        // Check if user is authenticated and doesn't have a role yet
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user already has a role assigned
        if ($user->roles()->exists()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Assign role to user using the relationship
        UserRole::create([
            'user_id'   => $user->id,
            'role_id'   => $request->role_id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Role selected successfully!');
    }
}
