<?php

namespace App\Nova\Filters;

use App\Models\WorkDetails;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Filters works by a column of their details (e.g. type or age_group).
 * Options are the distinct values stored for the user's team.
 */
class WorkDetailsFilter extends Filter
{
    public $component = 'select-filter';

    public function __construct(private string $column, string $name)
    {
        $this->name = $name;
    }

    public function key()
    {
        return 'work_details_' . $this->column;
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->whereHas('details', fn($q) => $q->where($this->column, $value));
    }

    public function options(NovaRequest $request)
    {
        return WorkDetails::query()
            ->whereNotNull($this->column)
            ->where($this->column, '!=', '')
            ->distinct()
            ->orderBy($this->column)
            ->pluck($this->column)
            ->mapWithKeys(fn($value) => [$value => $value])
            ->toArray();
    }
}