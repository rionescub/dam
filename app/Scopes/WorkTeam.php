<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scope to filter WorkDetails by the contest's team.
 */
class WorkTeam implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model    $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user === null || $user->is_super_admin()) {
            return;
        }

        $builder->whereHas('work.contest', function (Builder $query) use ($user): void {
            $query->where('team_id', $user->current_team_id);
        });
    }
}
