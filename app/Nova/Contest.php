<?php

namespace App\Nova;

use App\Nova\Resource;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Textarea;
use App\Nova\Metrics\ContestUsers;
use App\Nova\Metrics\ContestWorks;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MultiSelect;
use App\Nova\Metrics\RunningContests;
use Laravel\Nova\Actions\ExportAsCsv;
use App\Nova\Metrics\UpcomingContests;
use Laravel\Nova\Fields\BelongsToMany;
use App\Nova\Filters\ContestTypeFilter;
use App\Nova\Metrics\FinishingContests;
use Laravel\Nova\Http\Requests\NovaRequest;

class Contest extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Contest>
     */
    public static $model = \App\Models\Contest::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Date::make('Start Date')
                ->rules('required', 'after_or_equal:today'),

            Date::make('End Date')
                ->rules('required', 'after:start_date'),

            Date::make('Jury Date')
                ->rules('required', 'after:start_date'),

            Date::make('Ceremony Date')
                ->rules('required', 'after:start_date'),

            MultiSelect::make('Type')
                ->options([
                    'video' => 'Video',
                    'photo' => 'Photo',
                    'craft' => 'Craft',
                ])
                ->rules('required'),

            Select::make('Phase')
                ->options([
                    'local' => 'Local',
                    'national' => 'National',
                    'international' => 'International',
                ])
                ->rules('required'),

            BelongsTo::make('Parent Contest', 'parentContest', self::class)
                ->nullable(),

            Textarea::make('Rules')
                ->rules('required'),

            Textarea::make('Description')
                ->rules('required'),

            BelongsTo::make('User', 'creator', User::class)
                ->rules('required')
                ->default(Nova::user()->id)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            HasMany::make('Users')
                ->hideFromIndex(),

            HasMany::make('Works'),

            Text::make('Works Submitted', function () {
                return $this->works()->count();
            })
                ->sortable()
                ->onlyOnIndex(),

            BelongsToMany::make('Sponsors'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            ContestTypeFilter::make()
        ];
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
