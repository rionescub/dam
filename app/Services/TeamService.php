<?php

namespace App\Services;

use App\Models\User;

class TeamService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get all the teams that the user belongs to.
     *
     * @return array
     */
    public function getTeamIds()
    {
        if ($this->user->is_super_admin()) {
            // Admins do not need team filters.
            return [];
        }

        $teams = $this->user->currentTeam()
            ->withoutGlobalScopes()
            ->get()
            ->merge($this->user->teams()->withoutGlobalScopes()->get())
            ->sortBy('name');

        return $teams->pluck('id')->toArray();
    }
}
