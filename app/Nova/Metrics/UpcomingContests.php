<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Nova;
use App\Models\Contest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class UpcomingContests extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Contest::where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(14)));
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }
}
