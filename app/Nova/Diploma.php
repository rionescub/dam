<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Actions\GenerateDiplomaPDF;
use Laravel\Nova\Http\Requests\NovaRequest;

class Diploma extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Diploma>
     */
    public static $model = \App\Models\Diploma::class;
    public static function indexQuery(NovaRequest $request, $query)
    {
        $user = $request->user();

        // If the user is not an admin, filter by the contests they have access to through the Work relationship
        if (!$user->is_admin()) {
            $query->whereHas('work.contest', function ($contestQuery) use ($user) {
                $contestQuery->where('team_id', $user->current_team_id);
            });
        }

        return $query;
    }

    /**
     * Modify the query used to retrieve a single resource for details.
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        $user = $request->user();

        // If the user is not an admin, filter by the contests they have access to through the Work relationship
        if (!$user->is_admin()) {
            $query->whereHas('work.contest', function ($contestQuery) use ($user) {
                $contestQuery->where('team_id', $user->current_team_id);
            });
        }

        return $query;
    }

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

            BelongsTo::make('User')
                ->rules('required'),

            BelongsTo::make('Contest')
                ->rules('required'),

            BelongsTo::make('Work')
                ->rules('required'),

            HasOne::make('Score')
                ->rules('required'),

            Textarea::make('Description')
                ->rules('required'),
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
            (new GenerateDiplomaPDF())->showInline(),
        ];
    }
}
