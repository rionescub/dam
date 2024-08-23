<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;
use App\Nova\Metrics\RunningContests;
use App\Nova\Metrics\NewContests;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\NewWorks;

class ContestOverview extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new RunningContests(), // Number of contests currently running
            new NewContests(),     // Number of new contests
            new NewUsers(),        // Number of new users
            new NewWorks(),        // Number of new works
        ];
    }

    /**
     * The URI key for the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'main';
    }

    /**
     * Indicates whether to show on the Nova dashboard.
     *
     * @return bool
     */
    public static function displayInNavigation()
    {
        return true;
    }

    /**
     * The default dashboard for Nova.
     *
     * @return string
     */
    public static function defaultDashboard()
    {
        return static::uriKey();
    }
}
