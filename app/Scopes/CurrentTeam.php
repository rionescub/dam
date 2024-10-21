<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CurrentTeam implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // if (auth()->check()) {
        //     $builder->where('team_id', auth()->user()->current_team_id);
        // }
    }
}
