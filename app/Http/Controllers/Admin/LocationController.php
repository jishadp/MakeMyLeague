<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\District;
use App\Models\LocalBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * Display the admin location management dashboard.
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::withCount(['districts', 'localBodies'])->paginate(10);
        $districts = District::with(['state', 'localBodies'])->withCount('localBodies')->paginate(15);
        $localBodies = LocalBody::with(['district.state'])->paginate(20);

        return view('admin.locations.index', compact('states', 'districts', 'localBodies'));
    }

    /**
     * Show the form for creating a new state.
     */
    public function createState()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.locations.create-state');
    }

    /**
     * Store a newly created state.
     */
    public function storeState(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:states,name',
        ]);

        State::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'State created successfully!');
    }

    /**
     * Show the form for creating a new district.
     */
    public function createDistrict()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::orderBy('name')->get();
        return view('admin.locations.create-district', compact('states'));
    }

    /**
     * Store a newly created district.
     */
    public function storeDistrict(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        District::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'District created successfully!');
    }

    /**
     * Show the form for creating a new local body.
     */
    public function createLocalBody()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::orderBy('name')->get();
        $districts = District::with('state')->orderBy('name')->get();
        return view('admin.locations.create-local-body', compact('states', 'districts'));
    }

    /**
     * Store a newly created local body.
     */
    public function storeLocalBody(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
        ]);

        LocalBody::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Local Body created successfully!');
    }

    /**
     * Show the form for editing the specified state.
     */
    public function editState(State $state)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.locations.edit-state', compact('state'));
    }

    /**
     * Update the specified state.
     */
    public function updateState(Request $request, State $state)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:states,name,' . $state->id,
        ]);

        $state->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'State updated successfully!');
    }

    /**
     * Show the form for editing the specified district.
     */
    public function editDistrict(District $district)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::orderBy('name')->get();
        return view('admin.locations.edit-district', compact('district', 'states'));
    }

    /**
     * Update the specified district.
     */
    public function updateDistrict(Request $request, District $district)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $district->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'District updated successfully!');
    }

    /**
     * Show the form for editing the specified local body.
     */
    public function editLocalBody(LocalBody $localBody)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $states = State::orderBy('name')->get();
        $districts = District::with('state')->orderBy('name')->get();
        return view('admin.locations.edit-local-body', compact('localBody', 'states', 'districts'));
    }

    /**
     * Update the specified local body.
     */
    public function updateLocalBody(Request $request, LocalBody $localBody)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
        ]);

        $localBody->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Local Body updated successfully!');
    }

    /**
     * Remove the specified state.
     */
    public function destroyState(State $state)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if state has districts
        if ($state->districts()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete state. It has districts associated with it.');
        }

        $state->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'State deleted successfully!');
    }

    /**
     * Remove the specified district.
     */
    public function destroyDistrict(District $district)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if district has local bodies
        if ($district->localBodies()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete district. It has local bodies associated with it.');
        }

        $district->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'District deleted successfully!');
    }

    /**
     * Remove the specified local body.
     */
    public function destroyLocalBody(LocalBody $localBody)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Check if local body has users or leagues
        if ($localBody->users()->count() > 0 || $localBody->leagues()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete local body. It has users or leagues associated with it.');
        }

        $localBody->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Local Body deleted successfully!');
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
}
