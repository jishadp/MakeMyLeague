<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizerRequest;
use App\Models\LeagueOrganizer;
use App\Models\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerRequestController extends Controller
{
    /**
     * Display a listing of all organizer requests.
     */
    public function index()
    {
        $requests = OrganizerRequest::with(['user', 'league', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.organizer-requests.index', compact('requests'));
    }

    /**
     * Display pending organizer requests.
     */
    public function pending()
    {
        $requests = OrganizerRequest::with(['user', 'league'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.organizer-requests.pending', compact('requests'));
    }

    /**
     * Show the form for reviewing an organizer request.
     */
    public function show(OrganizerRequest $organizerRequest)
    {
        $organizerRequest->load(['user', 'league', 'reviewer']);

        return view('admin.organizer-requests.show', compact('organizerRequest'));
    }

    /**
     * Approve an organizer request.
     */
    public function approve(Request $request, OrganizerRequest $organizerRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($organizerRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }

        // Update the organizer request
        $organizerRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update or create the league organizer relationship
        LeagueOrganizer::updateOrCreate(
            [
                'league_id' => $organizerRequest->league_id,
                'user_id' => $organizerRequest->user_id,
            ],
            [
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
            ]
        );

        // Automatically assign Organiser role to the user
        $organizerRole = Role::where('name', User::ROLE_ORGANISER)->first();
        if ($organizerRole) {
            // Check if user already has this role
            $existingRole = UserRole::where('user_id', $organizerRequest->user_id)
                ->where('role_id', $organizerRole->id)
                ->first();
            
            if (!$existingRole) {
                UserRole::create([
                    'user_id' => $organizerRequest->user_id,
                    'role_id' => $organizerRole->id,
                ]);
            }
        }

        return redirect()->route('admin.organizer-requests.pending')
            ->with('success', 'Organizer request has been approved successfully.');
    }

    /**
     * Reject an organizer request.
     */
    public function reject(Request $request, OrganizerRequest $organizerRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        if ($organizerRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }

        // Update the organizer request
        $organizerRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update or create the league organizer relationship as rejected
        LeagueOrganizer::updateOrCreate(
            [
                'league_id' => $organizerRequest->league_id,
                'user_id' => $organizerRequest->user_id,
            ],
            [
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
            ]
        );

        return redirect()->route('admin.organizer-requests.pending')
            ->with('success', 'Organizer request has been rejected.');
    }

    /**
     * Change league status (Admin power).
     */
    public function changeLeagueStatus(Request $request, OrganizerRequest $organizerRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Update the league status
        $organizerRequest->league->update([
            'status' => $request->status,
        ]);

        // Log the status change in admin notes
        if ($request->admin_notes) {
            $organizerRequest->update([
                'admin_notes' => $request->admin_notes,
            ]);
        }

        return redirect()->route('admin.organizer-requests.show', $organizerRequest)
            ->with('success', 'League status has been updated to ' . ucfirst($request->status) . '.');
    }

    /**
     * Get statistics for organizer requests.
     */
    public function stats()
    {
        $stats = [
            'total' => OrganizerRequest::count(),
            'pending' => OrganizerRequest::where('status', 'pending')->count(),
            'approved' => OrganizerRequest::where('status', 'approved')->count(),
            'rejected' => OrganizerRequest::where('status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }
}