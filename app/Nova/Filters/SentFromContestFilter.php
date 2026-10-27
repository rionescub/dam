<?php

namespace App\Nova\Filters;

use App\Models\Contest;
use App\Models\Work;
use App\Scopes\ContestTeam;
use App\Scopes\CurrentTeam;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * International works cloned from one national contest.
 */
class SentFromContestFilter extends Filter
{
    public $component = 'select-filter';

    public $name = 'Sent from';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->whereIn('source_work_id', $this->worksOf()->where('contest_id', $value)->select('id'));
    }

    public function options(NovaRequest $request)
    {
        $sentWorks = $this->worksOf()->whereIn('id', $this->worksOf()->whereNotNull('source_work_id')->select('source_work_id'));

        return Contest::withoutGlobalScope(CurrentTeam::class)
            ->whereIn('id', $sentWorks->select('contest_id'))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($contest) => [$contest->name => $contest->id])
            ->toArray();
    }

    /** Works of every team, including deleted ones: national works are another team's. */
    private function worksOf()
    {
        return Work::withoutGlobalScope(ContestTeam::class)->withTrashed();
    }
}
