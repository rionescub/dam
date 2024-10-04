<?php

namespace App\Policies;

use App\Models\Score;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScorePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_organizer();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Score $Score): bool
    {
        return $user->is_organizer();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Score $Score): bool
    {
        return $user->is_admin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Score $Score): bool
    {
        return $user->is_admin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Score $Score): bool
    {
        return $user->is_admin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Score $Score): bool
    {
        return $user->is_admin();
    }
}
