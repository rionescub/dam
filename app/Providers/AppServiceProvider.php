<?php

namespace App\Providers;

use App\Services\InternationalContestSender;
use App\Observers\SettingObserver;
use Illuminate\Support\ServiceProvider;
use Outl1ne\NovaSettings\NovaSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One per request, so the Contests list looks up the open EN contest once, not per row
        $this->app->scoped(InternationalContestSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
