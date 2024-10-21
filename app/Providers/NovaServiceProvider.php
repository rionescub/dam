<?php

namespace App\Providers;

use App\Nova\Team;
use App\Nova\User;
use App\Nova\Work;
use App\Nova\Score;
use App\Nova\Contact;
use App\Nova\Contest;
use App\Nova\Diploma;
use App\Nova\Gallery;
use App\Nova\Sponsor;
use Laravel\Nova\Nova;
use App\Nova\WorkDetails;
use Illuminate\Http\Request;

use NormanHuth\NovaMenu\MenuItem;
use Illuminate\Support\Facades\Gate;
use NormanHuth\NovaMenu\MenuSection;
use App\Nova\Dashboards\ContestOverview;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->register_menu();

        Nova::footer(function ($request) {
            return '<p class="text-center my-3">Copyright &copy; 2024 - ' . date('Y') . ' Danube Art Master</p>';
        });


        Nova::logo(function () {
            return '<b>DAM</b> Admin';
        });
    }

    public function register_menu()
    {
        Nova::mainMenu(function (Request $request) {
            return [
                // Dashboard Section
                MenuSection::dashboard(ContestOverview::class)
                    ->icon('chart-bar'),

                // User Management Section
                MenuSection::make('Users', [
                    MenuItem::resource(User::class)->icon('user'),
                    MenuItem::resource(Team::class)->icon('user-group'),
                ])->collapsable(),

                // Contest Management Section
                MenuSection::make('Contests', [
                    MenuItem::resource(Contest::class)->icon('calendar'),
                    MenuItem::resource(Sponsor::class)->icon('currency-dollar'),
                ])->collapsable(),

                // Work Management Section
                MenuSection::make('Artworks', [
                    MenuItem::resource(Work::class)->icon('briefcase'),
                    MenuItem::resource(WorkDetails::class)->icon('document-text'),
                    MenuItem::resource(Score::class)->icon('star'),
                    MenuItem::resource(Diploma::class)->icon('academic-cap'),
                ])->collapsable(),

                // Media & Contacts Section
                MenuSection::make('Other', [
                    MenuItem::resource(Gallery::class)->icon('photograph'),
                    MenuItem::resource(Contact::class)->icon('mail'),
                ])->collapsable(),
            ];
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user->is_organizer();
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new ContestOverview(),
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
