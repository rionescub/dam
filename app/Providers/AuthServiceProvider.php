<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Work;
use App\Models\Score;
use App\Models\Contest;
use App\Models\Diploma;
use App\Policies\UserPolicy;
use App\Policies\WorkPolicy;
use App\Policies\ScorePolicy;
use App\Policies\ContestPolicy;
use App\Policies\DiplomaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Add your model => policy mappings here

        Contest::class => ContestPolicy::class,
        User::class => UserPolicy::class,
        Work::class => WorkPolicy::class,
        Score::class => ScorePolicy::class,
        Diploma::class => DiplomaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
