<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Resource;
use Laravel\Nova\Http\Requests\NovaRequest;

class Contact extends Resource
{
    public static $model = \App\Models\Contact::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'email',
        'subject',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Team')->sortable()->rules('required'), // Associate contact with a team
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Email')->sortable()->rules('required', 'email'),
            Text::make('Subject')->rules('required', 'max:255'),
            Textarea::make('Comments')->rules('required'),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }
}
