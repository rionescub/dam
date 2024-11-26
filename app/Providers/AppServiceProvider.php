<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
