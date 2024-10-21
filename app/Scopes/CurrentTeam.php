<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CurrentTeam implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();

        // Only apply the scope if the user is authenticated and is not an admin
        if ($user && !$user->is_admin()) {
            $builder->where('team_id', $user->current_team_id);
        }
    }
}
