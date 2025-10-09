<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->whereHas('roles', function($q) {
                $q->where('name', User::ROLE_ADMIN);
            })
            ->with('roles');

        // Search by name or mobile
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $adminUsers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.admin-users.index', compact('adminUsers'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create(): View
    {
        return view('admin.admin-users.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:users,mobile',
            'email' => 'nullable|email|unique:users,email',
            'pin' => 'required|string|min:4|max:6',
            'confirm_pin' => 'required|same:pin',
        ], [
            'mobile.unique' => 'This mobile number is already registered.',
            'email.unique' => 'This email address is already registered.',
            'pin.min' => 'PIN must be at least 4 digits.',
            'pin.max' => 'PIN cannot exceed 6 digits.',
            'confirm_pin.same' => 'PIN confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'pin' => Hash::make($request->pin),
                'slug' => Str::slug($request->name),
            ]);

            // Get or create Admin role
            $adminRole = Role::firstOrCreate(['name' => User::ROLE_ADMIN]);

            // Assign admin role to user
            $user->roles()->attach($adminRole->id);

            return redirect()->route('admin.admin-users.index')
                ->with('success', 'Admin user created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create admin user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified admin user.
     */
    public function show(User $adminUser): View
    {
        // Ensure the user is an admin
        if (!$adminUser->isAdmin()) {
            abort(404);
        }

        $adminUser->load('roles');
        
        return view('admin.admin-users.show', compact('adminUser'));
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $adminUser): View
    {
        // Ensure the user is an admin
        if (!$adminUser->isAdmin()) {
            abort(404);
        }

        return view('admin.admin-users.edit', compact('adminUser'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, User $adminUser): RedirectResponse
    {
        // Ensure the user is an admin
        if (!$adminUser->isAdmin()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:users,mobile,' . $adminUser->id,
            'email' => 'nullable|email|unique:users,email,' . $adminUser->id,
            'pin' => 'nullable|string|min:4|max:6',
            'confirm_pin' => 'nullable|same:pin',
        ], [
            'mobile.unique' => 'This mobile number is already registered.',
            'email.unique' => 'This email address is already registered.',
            'pin.min' => 'PIN must be at least 4 digits.',
            'pin.max' => 'PIN cannot exceed 6 digits.',
            'confirm_pin.same' => 'PIN confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
            ];

            // Update PIN if provided
            if ($request->filled('pin')) {
                $updateData['pin'] = Hash::make($request->pin);
            }

            $adminUser->update($updateData);

            return redirect()->route('admin.admin-users.index')
                ->with('success', 'Admin user updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update admin user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(User $adminUser): RedirectResponse
    {
        // Ensure the user is an admin
        if (!$adminUser->isAdmin()) {
            abort(404);
        }

        // Prevent deleting the current user
        if ($adminUser->id === auth()->id()) {
            return redirect()->back()
                ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        try {
            // Remove admin role
            $adminRole = Role::where('name', User::ROLE_ADMIN)->first();
            if ($adminRole) {
                $adminUser->roles()->detach($adminRole->id);
            }

            // Delete the user
            $adminUser->delete();

            return redirect()->route('admin.admin-users.index')
                ->with('success', 'Admin user deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete admin user: ' . $e->getMessage()]);
        }
    }

    /**
     * Reset admin user PIN.
     */
    public function resetPin(User $adminUser)
    {
        // Ensure the user is an admin
        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {
            // Generate a random 4-digit PIN
            $newPin = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            // Update the admin user's PIN
            $adminUser->update([
                'pin' => Hash::make($newPin)
            ]);

            return response()->json([
                'success' => true,
                'message' => "Admin user PIN has been reset successfully.",
                'new_pin' => $newPin,
                'user_name' => $adminUser->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting PIN: ' . $e->getMessage()
            ], 500);
        }
    }
}
