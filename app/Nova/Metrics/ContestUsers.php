<?php

namespace App\Nova\Metrics;

use App\Models\Work;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ContestUsers extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        // Assuming $request->resourceId is the ID of the contest for which this metric is being calculated
        $contestId = $request->resourceId;

        // Count distinct users who have submitted works to this contest
        return $this->result(
            Work::where('contest_id', $contestId)
                ->distinct('user_id')
                ->count('user_id')
        );
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
        ];
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
