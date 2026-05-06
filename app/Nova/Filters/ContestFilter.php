<?php

namespace App\Nova\Filters;

use App\Models\Contest;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ContestFilter extends Filter
{
    public $component = 'select-filter';

    public function __construct(private bool $throughWork = false) {}

    public function apply(NovaRequest $request, $query, $value)
    {
        if ($this->throughWork) {
            return $query->whereHas('work', fn($q) => $q->where('contest_id', $value));
        }

        return $query->where('contest_id', $value);
    }

    public function options(NovaRequest $request)
    {
        $user = $request->user();
        $query = Contest::withoutGlobalScopes()->orderBy('name');

        if (!$user->is_super_admin()) {
            $query->where('team_id', $user->current_team_id);
        }

        return $query->get()->mapWithKeys(fn($c) => [$c->name => $c->id])->toArray();
    }
}