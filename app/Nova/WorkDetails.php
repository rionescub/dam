<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class WorkDetails extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\WorkDetails>
     */
    public static $model = \App\Models\WorkDetails::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'full_name',
        'phone',
        'school',
        'mentor',
        'school_director'
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

            BelongsTo::make('Work'),

            Text::make('Full Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Date::make('Date of Birth')
                ->rules('required', 'date'),

            Text::make('Phone')
                ->sortable()
                ->rules('required', 'max:20'),

            Text::make('Mentor')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('School')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('School Director')
                ->sortable()
                ->rules('nullable', 'max:255'),
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
        return [];
    }
}
