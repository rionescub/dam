<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public function title()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->is_super_admin()) {
            return $query;
        }
        return $query->where('current_team_id', $request->user()->current_team_id);
    }
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'first_name',
        'last_name',
        'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('First Name')->sortable()->rules('required', 'max:255'),
            Text::make('Last Name')->sortable()->rules('required', 'max:255'),
            Select::make('Role')->options([
                'admin' => 'Admin',
                'organizer' => 'Organizer',
                'judge' => 'Judge',
                'teacher' => 'Teacher',
                'contestant' => 'Contestant',
                'user' => 'User',
            ])->displayUsingLabels()->sortable()->rules('required'),

            //  Date::make('Date of Birth')->rules('required'),

            Text::make('Email')->sortable()->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            BelongsTo::make('Current Team', 'currentTeam', Team::class)
                ->nullable()
                ->displayUsing(function ($team) {
                    return $team->name;
                })
                ->hideFromIndex()
                ->hideFromDetail(function (Request $request) {
                    return !$request->user()->is_super_admin();
                })
                ->fillUsing(function (NovaRequest $request, $model, $attribute, $requestAttribute) {
                    if (!$request->user()->is_super_admin()) {
                        $model->current_team_id = $request->user()->current_team_id;
                    } else {
                        $model->$attribute = $request->$requestAttribute;
                    }
                })
                ->default(function (Request $request) {
                    return $request->user()->is_super_admin() ? null : $request->user()->current_team_id;
                }),


            Password::make('Password')->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),


            DateTime::make('Email Verified At', 'email_verified_at')
                ->onlyOnDetail()
                ->hideFromDetail()
                ->rules('required')
                ->default(now())
                ->creationRules('after_or_equal:now'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            ExportAsCsv::make()->withFormat(
                function ($model) {
                    return [
                        'id' => $model->id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'email' => $model->email,
                        'role' => $model->role,
                        'date_of_birth' => $model->date_of_birth,
                    ];
                }
            ),

        ];
    }
}
