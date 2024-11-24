<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Metrics\ContestUsers;
use App\Nova\Metrics\ContestWorks;
use App\Nova\Metrics\RunningContests;
use App\Nova\Metrics\UpcomingContests;
use App\Nova\Filters\ContestTypeFilter;
use App\Nova\Metrics\FinishingContests;
use Laravel\Nova\Actions\ExportAsCsv;

class Contest extends Resource
{
    public static $model = \App\Models\Contest::class;
    public static $title = 'name';
    public static $search = ['id', 'name', 'phase'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Date::make('Start Date')
                ->rules('required',)
                ->sortable(),

            Date::make('End Date')
                ->rules('required', 'after:start_date')
                ->sortable(),

            Date::make('Jury Date')
                ->rules('required', 'after:end_date')
                ->sortable(),

            Date::make('Ceremony Date')
                ->rules('required', 'after:jury_date')
                ->sortable(),

            // MultiSelect::make('Type')
            //     ->options([
            //         'video' => 'Video',
            //         'photo' => 'Photo',
            //         'craft' => 'Craft',
            //     ]),

            Select::make('Phase')
                ->options([
                    'local' => 'Local',
                    'national' => 'National',
                    'international' => 'International',
                ])
                ->rules('required')
                ->sortable(),

            BelongsTo::make('Parent Contest', 'parentContest', self::class)
                ->nullable(),

            Textarea::make('Rules')
                ->rules('required'),

            Textarea::make('Description')
                ->rules('required'),

            BelongsTo::make('User', 'creator', User::class)
                ->rules('required')
                ->withMeta(['value' => auth()->id()]) // Setting default value to current user ID
                ->hideFromIndex(),

            HasMany::make('Users')
                ->hideFromIndex(),

            HasMany::make('Works'),

            Text::make('Works Submitted', function () {
                return $this->works()->count();
            })
                ->sortable()
                ->onlyOnIndex(),

            BelongsToMany::make('Sponsors'),

            Select::make('Team', 'team_id')
                ->options(function () {
                    return \App\Models\Team::all()->pluck('name', 'id');
                })
                ->rules('required')
                ->default(auth()->user()->current_team_id)
                ->hideFromIndex(),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            ContestTypeFilter::make()
        ];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function cards(NovaRequest $request)
    {
        return [
            new RunningContests(),
            new FinishingContests(),
            new UpcomingContests(),
            (new ContestUsers())->onlyOnDetail(),
            (new ContestWorks())->onlyOnDetail(),
        ];
    }

    public function actions(NovaRequest $request)
    {
        return [
            ExportAsCsv::make()->withFormat(
                function ($model) {
                    return [
                        'id' => $model->id,
                        'name' => $model->name,
                        'start_date' => $model->start_date,
                        'end_date' => $model->end_date,
                        'phase' => $model->phase,
                        'type' => $model->type,
                        'parent_contest_id' => $model->parent_contest_id,
                        'rules' => $model->rules,
                        'description' => $model->description,
                        'user_id' => $model->user_id,
                    ];
                }
            ),
        ];
    }
}
