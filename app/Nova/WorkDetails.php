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

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Work'),

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
