<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MyTeams implements Scope
{

    public function __construct() {}
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        dd(auth()->guard('sanctum')->user());
        $user = auth()->guard('sanctum')->user();
        if (!$user || $user->isAdmin) {
            return;
        }

        $teams = $user->ownedTeams()->withoutGlobalScopes([MyTeams::class])->get()->merge($user->teams()->withoutGlobalScopes([MyTeams::class])->get())->sortBy('name');

        $ids = $teams->pluck('id')->toArray();

        $builder->whereIn('teams.id', $ids);
    }
}
