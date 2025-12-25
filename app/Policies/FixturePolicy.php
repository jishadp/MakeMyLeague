<?php

namespace App\Policies;

use App\Models\Fixture;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FixturePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fixture $fixture): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the scoring console.
     */
    public function viewScoringConsole(User $user, Fixture $fixture): bool
    {
        return $user->isAdmin() || 
               $user->isOrganizerForLeague($fixture->league_id) || 
               $user->id === $fixture->scorer_id;
    }

    /**
     * Determine whether the user can assign a scorer (Organizer/Admin only).
     */
    public function assignScorer(User $user, Fixture $fixture): bool
    {
        return $user->isAdmin() || $user->isOrganizerForLeague($fixture->league_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fixture $fixture): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fixture $fixture): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fixture $fixture): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fixture $fixture): bool
    {
        return false;
    }
}
