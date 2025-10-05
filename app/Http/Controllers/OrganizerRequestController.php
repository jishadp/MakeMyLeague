<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\OrganizerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerRequestController extends Controller
{
    /**
     * Display a listing of organizer requests made by the authenticated user.
     */
    public function index()
    {
        $requests = Auth::user()->organizerRequests()
            ->with(['league', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('organizer-requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new organizer request.
     */
    public function create()
    {
        $leagues = League::where('status', 'active')
            ->whereDoesntHave('organizers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        return view('organizer-requests.create', compact('leagues'));
    }

    /**
     * Store a newly created organizer request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'message' => 'nullable|string|max:1000',
        ]);

        // Check if user already has a request for this league
        $existingRequest = OrganizerRequest::where('user_id', Auth::id())
            ->where('league_id', $request->league_id)
            ->first();

        if ($existingRequest) {
            return redirect()->back()
                ->with('error', 'You already have a request for this league.');
        }

        // Check if user is already an organizer for this league
        $isAlreadyOrganizer = Auth::user()->isOrganizerForLeague($request->league_id);
        if ($isAlreadyOrganizer) {
            return redirect()->back()
                ->with('error', 'You are already an organizer for this league.');
        }

        OrganizerRequest::create([
            'user_id' => Auth::id(),
            'league_id' => $request->league_id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->route('organizer-requests.index')
            ->with('success', 'Your organizer request has been submitted successfully.');
    }

    /**
     * Display the specified organizer request.
     */
    public function show(OrganizerRequest $organizerRequest)
    {
        // Ensure the user can only view their own requests
        if ($organizerRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $organizerRequest->load(['league', 'reviewer']);

        return view('organizer-requests.show', compact('organizerRequest'));
    }

    /**
     * Cancel a pending organizer request.
     */
    public function cancel(OrganizerRequest $organizerRequest)
    {
        // Ensure the user can only cancel their own requests
        if ($organizerRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($organizerRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be cancelled.');
        }

        $organizerRequest->delete();

        return redirect()->route('organizer-requests.index')
            ->with('success', 'Your organizer request has been cancelled.');
    }
}