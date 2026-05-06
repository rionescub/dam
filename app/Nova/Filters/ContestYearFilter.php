<?php

namespace App\Nova\Filters;

use App\Models\Contest;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ContestYearFilter extends Filter
{
    public $component = 'select-filter';

    public $name = 'Contest Year';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->whereHas('contest', fn($q) => $q->whereYear('start_date', $value));
    }

    public function options(NovaRequest $request)
    {
        $user = $request->user();
        $query = Contest::withoutGlobalScopes()->selectRaw('YEAR(start_date) as year');

        if (!$user->is_super_admin()) {
            $query->where('team_id', $user->current_team_id);
        }

        $years = $query->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->map(fn($y) => (string) $y)
            ->push((string) now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        return $years->mapWithKeys(fn($y) => [$y => $y])->toArray();
    }

    public function default()
    {
        return (string) now()->year;
    }
}