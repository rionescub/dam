<?php

namespace App\Providers;

use App\Nova\Blog;
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
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;
use NormanHuth\NovaMenu\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use NormanHuth\NovaMenu\MenuSection;
use Outl1ne\NovaSettings\NovaSettings;
use App\Nova\Dashboards\ContestOverview;
use App\Nova\Testimonial;
use App\Repositories\TeamSettingsRepository;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Events\ServingNova;

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

        // Register settings when Nova is serving a request
        Nova::serving(function (ServingNova $event) {
            $this->register_settings();
        });

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
                    MenuItem::resource(Testimonial::class)->icon('star'),
                    MenuItem::resource(Contact::class)->icon('mail'),
                    MenuItem::resource(Blog::class)->icon('newspaper'),
                ])->collapsable(),

                // Settings Section
                MenuSection::make('Settings', [
                    MenuItem::make('Homepage')
                        ->path('/nova-settings/homepage')->icon('home'),
                    MenuItem::make('About')
                        ->path('/nova-settings/about')->icon('information-circle'),
                    MenuItem::make('Contact')
                        ->path('/nova-settings/contact')->icon('mail'),
                    MenuItem::make('Privacy')
                        ->path('/nova-settings/privacy')->icon('lock-closed'),
                    MenuItem::make('Rules')
                        ->path('/nova-settings/rules')->icon('document-text'),
                    MenuItem::make('Results')
                        ->path('/nova-settings/results')->icon('document-text'),
                    MenuItem::make('Translations')
                        ->path('/nova-settings/translations')->icon('translate'),
                    MenuItem::make('Terms')
                        ->path('/nova-settings/terms')->icon('document-text'),

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

        if (file_exists(base_path('routes/nova.php'))) {
            $this->loadRoutesFrom(base_path('routes/nova.php'));
        }
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
        return [
            new \Outl1ne\NovaSettings\NovaSettings,
        ];
    }

    /**
     * Register team-scoped settings.
     *
     * @return void
     */
    public function register_settings()
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $teamId = $user->current_team_id;

        if (!$teamId) {
            return;
        }


        $fields = [
            'Homepage' => $this->loadFieldsFrom(app_path('Nova/Settings/Homepage.php'), new TeamSettingsRepository($teamId, 'homepage')),
            'About' => $this->loadFieldsFrom(app_path('Nova/Settings/About.php'), new TeamSettingsRepository($teamId, 'about')),
            'Contact' => $this->loadFieldsFrom(app_path('Nova/Settings/Contact.php'), new TeamSettingsRepository($teamId, 'contact')),
            'Privacy' => $this->loadFieldsFrom(app_path('Nova/Settings/Privacy.php'), new TeamSettingsRepository($teamId, 'privacy')),
            'Rules' => $this->loadFieldsFrom(app_path('Nova/Settings/Rules.php'), new TeamSettingsRepository($teamId, 'rules')),
            'Results' => $this->loadFieldsFrom(app_path('Nova/Settings/Results.php'), new TeamSettingsRepository($teamId, 'results')),
            'Translations' => $this->loadFieldsFrom(app_path('Nova/Settings/Translations.php'), new TeamSettingsRepository($teamId, 'translations')),
            'Terms' => $this->loadFieldsFrom(app_path('Nova/Settings/Terms.php'), new TeamSettingsRepository($teamId, 'terms')),
        ];


        foreach ($fields as $section => $sectionFields) {
            NovaSettings::addSettingsFields(
                $sectionFields,
                [],
                $section
            );
        }
    }

    /**
     * Load fields from a given file and bind the repository.
     *
     * @param string $path
     * @param TeamSettingsRepository $teamSettingsRepo
     * @return array
     */
    protected function loadFieldsFrom(string $path, TeamSettingsRepository $teamSettingsRepo): array
    {
        if (file_exists($path)) {
            $fields = require $path;

            // Bind the repository to the fields
            foreach ($fields as $field) {
                $field->resolveUsing(function () use ($field, $teamSettingsRepo) {
                    return $teamSettingsRepo->getSetting($field->attribute);
                });

                $field->fillUsing(function ($request, $model, $attribute, $requestAttribute) use ($field, $teamSettingsRepo) {
                    $teamSettingsRepo->setSetting($attribute, $request->$requestAttribute);
                });
            }
            return $fields;
        }

        return [];
    }
}
