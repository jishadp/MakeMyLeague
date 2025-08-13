<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Ground;
use App\Models\LocalBody;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroundController extends Controller
{
    /**
     * Display a listing of the grounds.
     */
    public function index(Request $request): View
    {
        $query = Ground::query()
            ->with(['state', 'district', 'localBody']);
        
        // Filter by state
        if ($request->has('state_id') && $request->state_id != '') {
            $query->where('state_id', $request->state_id);
        }
        
        // Filter by district
        if ($request->has('district_id') && $request->district_id != '') {
            $query->where('district_id', $request->district_id);
        }
        
        // Filter by local body
        if ($request->has('localbody_id') && $request->localbody_id != '') {
            $query->where('localbody_id', $request->localbody_id);
        }
        
        // Filter by capacity
        if ($request->has('min_capacity') && $request->min_capacity != '') {
            $query->where('capacity', '>=', $request->min_capacity);
        }
        
        // Filter by availability
        if ($request->has('available') && $request->available != '') {
            $query->where('is_available', $request->available == '1');
        }
        
        // Sort by name or capacity
        if ($request->has('sort_by') && $request->sort_by != '') {
            $sortDirection = $request->has('sort_dir') && $request->sort_dir == 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $grounds = $query->paginate(12)->withQueryString();
        
        // Get all states, districts, and local bodies for filtering
        $states = State::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $localBodies = LocalBody::orderBy('name')->get();
        
        // Get active districts and local bodies based on filters
        $activeDistricts = collect();
        $activeLocalBodies = collect();
        
        if ($request->has('state_id') && $request->state_id != '') {
            $activeDistricts = District::where('state_id', $request->state_id)->orderBy('name')->get();
        }
        
        if ($request->has('district_id') && $request->district_id != '') {
            $activeLocalBodies = LocalBody::where('district_id', $request->district_id)->orderBy('name')->get();
        }
        
        return view('grounds.index', compact(
            'grounds', 
            'states', 
            'districts', 
            'localBodies', 
            'activeDistricts', 
            'activeLocalBodies'
        ));
    }

    /**
     * Display the specified ground.
     */
    public function show(Ground $ground): View
    {
        return view('grounds.show', compact('ground'));
    }
}
