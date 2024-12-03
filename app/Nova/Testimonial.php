<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;

class Testimonial extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Testimonials::class;

    /**
     * The single value that should be used to represent the resource.
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
        'name',
        'location'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'string', 'max:255'),

            Text::make('Location')
                ->sortable()
                ->rules('required', 'string', 'max:255'),

            Textarea::make('Description', 'text')
                ->rules('required'),


            Number::make('Rating')
                ->sortable()
                ->rules('required', 'integer', 'between:1,5')
                ->min(1)
                ->max(5),

            BelongsTo::make('Team', 'team', Team::class)
                ->rules('required')
                ->default(fn() => auth()->user()->current_team_id ?? throw new \Exception('Team ID is required'))
                ->fillUsing(fn($request, $model, $attribute, $requestAttribute) => $model->{$attribute} = auth()->user()->current_team_id ?? throw new \Exception('Team ID is required'))
                ->hideWhenCreating()
                ->withMeta(['value' => auth()->user()->current_team_id ?? null]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
