<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins and organizers can view users
        return $user->is_admin() || $user->is_organizer();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admins can view all users
        if ($user->is_admin()) {
            return true;
        }

        // Organizers can view users that belong to their team or are below them in role hierarchy
        if ($user->is_organizer() && $model->team_id === $user->current_team_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins can create any user
        if ($user->is_admin()) {
            return true;
        }

        // Organizers can create users, but only for their own team and with lower roles
        if ($user->is_organizer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admins can update any user
        if ($user->is_admin()) {
            return true;
        }

        // Organizers can update users of their own team, but not other organizers or admins
        if ($user->is_organizer() && $model->team_id === $user->current_team_id && !$model->is_admin() && !$model->is_organizer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admins can delete any user
        if ($user->is_admin()) {
            return true;
        }

        // Organizers can delete users of their own team, but not other organizers or admins
        if ($user->is_organizer() && $model->team_id === $user->current_team_id && !$model->is_admin() && !$model->is_organizer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Admins can restore any user
        if ($user->is_admin()) {
            return true;
        }

        // Organizers can restore users of their own team, but not other organizers or admins
        if ($user->is_organizer() && $model->team_id === $user->current_team_id && !$model->is_admin() && !$model->is_organizer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Admins can force delete any user
        if ($user->is_admin()) {
            return true;
        }

        // Organizers cannot permanently delete users
        return false;
    }
}
