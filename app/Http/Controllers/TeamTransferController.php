<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeamTransferController extends Controller
{
    /**
     * Transfer team ownership to another user.
     */
    public function transfer(Request $request, Team $team)
    {
        // Check if the current user is authorized to transfer this team
        if (!$this->canTransferTeam($team)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to transfer this team.'
            ], 403);
        }

        $request->validate([
            'new_owner_id' => 'required|exists:users,id'
        ]);

        // Additional validation: Check if the new owner is the same as the current user
        if ($request->new_owner_id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot transfer the team to yourself.'
            ], 422);
        }

        $newOwner = User::findOrFail($request->new_owner_id);

        // Check if the new owner already owns too many teams (optional limit)
        $newOwnerTeamCount = $newOwner->primaryOwnedTeams()->count();
        if ($newOwnerTeamCount >= 10) { // Set a reasonable limit
            return response()->json([
                'success' => false,
                'message' => 'The selected user already owns the maximum number of teams allowed.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update team ownership in both old and new systems for backward compatibility
            $team->update(['owner_id' => $newOwner->id]);

            // Update team_owners pivot table - remove old owner and add new owner
            $team->owners()->wherePivot('role', 'owner')->detach();
            $team->owners()->attach($newOwner->id, ['role' => 'owner']);

            // Assign Team Owner role to the new owner
            $this->assignOwnerRole($newOwner);

            // Remove Team Owner role from the old owner if they don't own any other teams
            $this->removeOwnerRoleIfNeeded(Auth::user());

            // TODO: Send notification to the new owner
            // $this->sendTeamTransferNotification($newOwner, $team, Auth::user());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team transferred successfully.',
                'new_owner' => [
                    'id' => $newOwner->id,
                    'name' => $newOwner->name,
                    'email' => $newOwner->email
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer team. Please try again.'
            ], 500);
        }
    }

    /**
     * Search users for team transfer.
     */
    public function searchUsers(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');
        
        $users = User::where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->where('id', '!=', Auth::id()) // Exclude current user
            ->where('id', '!=', 1) // Exclude admin user
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->mobile ? ($user->country_code . ' ' . $user->mobile) : null,
                    'team_count' => $user->primaryOwnedTeams()->count()
                ];
            })
        ]);
    }

    /**
     * Check if the current user can transfer a team.
     */
    private function canTransferTeam(Team $team): bool
    {
        $user = Auth::user();
        
        // Check if user is the primary owner using the new relationship
        if ($user->isOwnerOfTeam($team->id)) {
            return true;
        }

        // Check if user is a co-owner with transfer permissions (you can add this logic)
        if ($user->isCoOwnerOfTeam($team->id)) {
            return true; // Or add specific transfer permission check
        }

        return false;
    }

    /**
     * Send notification to new owner when team is transferred.
     */
    private function sendTeamTransferNotification($newOwner, $team, $previousOwner)
    {
        \App\Models\Notification::create([
            'user_id' => $newOwner->id,
            'type' => 'team_transferred',
            'title' => 'Team Ownership Transferred',
            'message' => "You have been transferred ownership of the team '{$team->name}' by {$previousOwner->name}.",
            'data' => [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'previous_owner_id' => $previousOwner->id,
                'previous_owner_name' => $previousOwner->name,
            ]
        ]);
    }

    /**
     * Assign Team Owner role to a user.
     */
    private function assignOwnerRole(User $user)
    {
        $ownerRole = Role::where('name', User::ROLE_OWNER)->first();
        if ($ownerRole) {
            // Check if user already has this role
            $existingRole = UserRole::where('user_id', $user->id)
                ->where('role_id', $ownerRole->id)
                ->first();
            
            if (!$existingRole) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $ownerRole->id,
                ]);
            }
        }
    }

    /**
     * Remove Team Owner role from a user if they don't own any other teams.
     */
    private function removeOwnerRoleIfNeeded(User $user)
    {
        // Check if user still owns any teams
        $ownedTeamsCount = $user->primaryOwnedTeams()->count();
        
        if ($ownedTeamsCount === 0) {
            // User doesn't own any teams anymore, remove the Owner role
            $ownerRole = Role::where('name', User::ROLE_OWNER)->first();
            if ($ownerRole) {
                UserRole::where('user_id', $user->id)
                    ->where('role_id', $ownerRole->id)
                    ->delete();
            }
        }
    }
}