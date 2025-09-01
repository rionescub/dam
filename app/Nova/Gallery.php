<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class Gallery extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Gallery>
     */
    public static $model = \App\Models\Gallery::class;

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
        'title',
        'contestant',
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->is_super_admin()) {
            return $query;
        }
        return $query->where('team_id', $request->user()->current_team_id);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields($request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),
            Image::make('Image')
                ->disk('public')
                ->path('old')
                ->rules('required', 'image'),
            Text::make('Contestant')
                ->sortable()
                ->rules('required', 'max:255'),
            Number::make('Year')
                ->sortable()
                ->rules('required')
                ->default(now()->year),
            Select::make('Team', 'team_id')
                ->options(function () use ($request) {
                    if ($request->user()->is_super_admin()) {
                        return \App\Models\Team::all()->pluck('name', 'id');
                    } else {
                        return \App\Models\Team::where('id', $request->user()->current_team_id)->pluck('name', 'id');
                    }
                })
                ->rules('required')
                ->default(auth()->user()->current_team_id)
                ->displayUsing(function ($team) {
                    return \App\Models\Team::find($team)->name;
                })
                ->hideFromIndex(),
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
