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
            'contact_email' => 'nullable|email|max:255',
            'facilities' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('grounds', 'public');
                $imagePaths[] = $path;
            }
        }

        $validated['images'] = $imagePaths;
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
            'contact_email' => 'nullable|email|max:255',
            'facilities' => 'nullable|string',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
        ]);

        // Handle new image uploads
        $newImagePaths = [];
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('grounds', 'public');
                $newImagePaths[] = $path;
            }
        }

        // Handle image removal
        $currentImages = $ground->images ?? [];
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imageToRemove) {
                // Remove from storage
                if (Storage::disk('public')->exists($imageToRemove)) {
                    Storage::disk('public')->delete($imageToRemove);
                }
                // Remove from array
                $currentImages = array_filter($currentImages, function($image) use ($imageToRemove) {
                    return $image !== $imageToRemove;
                });
            }
        }

        // Merge existing images with new ones
        $validated['images'] = array_merge($currentImages, $newImagePaths);
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

        // Delete associated images
        if ($ground->images) {
            foreach ($ground->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
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
