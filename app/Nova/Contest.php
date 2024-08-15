<?php

namespace App\Nova;

use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
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
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Date::make('Start Date')->rules('required', 'date'),
            Date::make('End Date')->rules('required', 'date'),
            Text::make('Type')->sortable()->rules('required', 'max:255'),
            BelongsTo::make('Parent Contest', 'parentContest', self::class)->nullable(),
            Textarea::make('Rules')->rules('required'),
            Textarea::make('Description')->rules('required'),
            BelongsTo::make('User')->rules('required'),
            BelongsToMany::make('Users'),
            HasMany::make('Works'), // Add this line for works relationship
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
