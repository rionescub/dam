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
    public static $model = \App\Models\WorkDetails::class;

    public static function searchableColumns()
    {
        return [
            'full_name',
            'country',
            'county',
            'city',
            'mentor',
            'phone',
            'school',
            'year',
            'type',
            'age_group',
        ];
    }
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
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Work')
                ->filter(function ($query, $request) {
                    $user = $request->user();
                    $query->whereHas('contest', function ($contestQuery) use ($user) {
                        $contestQuery->where('team_id', $user->current_team_id);
                    });
                }),

            Text::make('Full Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Country')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('County')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('City')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Mentor')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('Phone')
                ->sortable()
                ->rules('nullable', 'max:20'),

            Text::make('School')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('School Director')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('Year')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('Age Group')
                ->sortable()
                ->rules('required'),

            Text::make('Type')
                ->sortable()
                ->rules('required'),
        ];
    }
}
