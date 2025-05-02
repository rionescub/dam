<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Apply team-based filtering via the related contest.
 */
class ContestTeam implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user === null || $user->is_super_admin()) {
            return;
        }

        $builder->whereHas('contest', function (Builder $query) use ($user) {
            $query->where('team_id', $user->current_team_id);
        });
    }
}
