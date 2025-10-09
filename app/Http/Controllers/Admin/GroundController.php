<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ground;
use App\Models\State;
use App\Models\District;
use App\Models\LocalBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroundController extends Controller
{
    /**
     * Display the admin ground management dashboard.
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $grounds = Ground::with(['state', 'district', 'localBody'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.grounds.index', compact('grounds'));
    }

    /**
     * Show the form for creating a new ground.
     */
    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::orderBy('name')->get();
        $districts = District::with('state')->orderBy('name')->get();
        $localBodies = LocalBody::with(['district.state'])->orderBy('name')->get();

        return view('admin.grounds.create', compact('states', 'districts', 'localBodies'));
    }

    /**
     * Store a newly created ground.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'address' => 'required|string|max:500',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'localbody_id' => 'required|exists:local_bodies,id',
            'is_available' => 'boolean',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageFilename = 'ground_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('grounds', $imageFilename, 'public');
            $validated['image'] = $imagePath;
        }

        $validated['is_available'] = $request->has('is_available');

        Ground::create($validated);

        return redirect()->route('admin.grounds.index')
            ->with('success', 'Ground created successfully!');
    }

    /**
     * Display the specified ground.
     */
    public function show(Ground $ground)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $ground->load(['state', 'district', 'localBody']);
        return view('admin.grounds.show', compact('ground'));
    }

    /**
     * Show the form for editing the specified ground.
     */
    public function edit(Ground $ground)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::orderBy('name')->get();
        $districts = District::with('state')->orderBy('name')->get();
        $localBodies = LocalBody::with(['district.state'])->orderBy('name')->get();

        return view('admin.grounds.edit', compact('ground', 'states', 'districts', 'localBodies'));
    }

    /**
     * Update the specified ground.
     */
    public function update(Request $request, Ground $ground)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'address' => 'required|string|max:500',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'localbody_id' => 'required|exists:local_bodies,id',
            'is_available' => 'boolean',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($ground->image && Storage::disk('public')->exists($ground->image)) {
                Storage::disk('public')->delete($ground->image);
            }
            
            // Upload new image
            $image = $request->file('image');
            $imageFilename = 'ground_' . $ground->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('grounds', $imageFilename, 'public');
            $validated['image'] = $imagePath;
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($ground->image && Storage::disk('public')->exists($ground->image)) {
                Storage::disk('public')->delete($ground->image);
            }
            $validated['image'] = null;
        }

        $validated['is_available'] = $request->has('is_available');

        $ground->update($validated);

        return redirect()->route('admin.grounds.index')
            ->with('success', 'Ground updated successfully!');
    }

    /**
     * Remove the specified ground.
     */
    public function destroy(Ground $ground)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if ground is being used by teams or leagues
        if ($ground->teams()->count() > 0) {
            return redirect()->route('admin.grounds.index')
                ->with('error', 'Cannot delete ground. It is being used by teams.');
        }

        // Delete associated image
        if ($ground->image && Storage::disk('public')->exists($ground->image)) {
            Storage::disk('public')->delete($ground->image);
        }

        $ground->delete();

        return redirect()->route('admin.grounds.index')
            ->with('success', 'Ground deleted successfully!');
    }

    /**
     * Toggle ground availability.
     */
    public function toggleAvailability(Ground $ground)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $ground->update(['is_available' => !$ground->is_available]);

        $status = $ground->is_available ? 'available' : 'unavailable';
        return redirect()->route('admin.grounds.index')
            ->with('success', "Ground marked as {$status}!");
    }

    /**
     * Get districts by state for AJAX requests.
     */
    public function getDistrictsByState(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $districts = District::where('state_id', $request->state_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    /**
     * Get local bodies by district for AJAX requests.
     */
    public function getLocalBodiesByDistrict(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $localBodies = LocalBody::where('district_id', $request->district_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($localBodies);
    }
}
