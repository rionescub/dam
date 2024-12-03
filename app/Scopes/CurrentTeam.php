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
        if ($user && $user->is_super_admin()) {
            $builder;
        } else {
            $builder->where('team_id', $user->current_team_id);
        }
    }
}
