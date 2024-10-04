<?php

namespace App\Nova;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Http\Requests\NovaRequest;

class Work extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Work>
     */
    public static $model = \App\Models\Work::class;


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

            Image::make('Image')
                ->nullable(),

            Text::make('Video URL')
                ->nullable(),

            Textarea::make('Description')
                ->rules('required'),

            Number::make('Rank')
                ->min(1)
                ->readonly()
                ->rules('required', 'integer', 'min:1'),

            Number::Make('Total Score')
                ->readonly(),

            BelongsTo::make('Contest')
                ->rules('required'),

            BelongsTo::make('User')
                ->rules('required'),

            HasMany::make('Scores'),
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
            ExportAsCsv::make()->withFormat(
                function ($model) {
                    return [
                        'id' => $model->getKey(),
                        'name' => $model->name,
                        'image' => $model->image,
                        'video_url' => $model->video_url,
                        'description' => $model->description,
                        'rank' => $model->rank,
                        'total_score' => $model->total_score,
                        'contest_name' => $model->contest->name,
                        'user_name' => $model->user->name,
                        'work_details_full_name' => $model->details->full_name,
                        'work_details_date_of_birth' => $model->details->date_of_birth,
                        'work_details_phone' => $model->details->phone,
                        'work_details_mentor' => $model->details->mentor,
                        'work_details_school' => $model->details->school,
                        'work_details_school_director' => $model->details->school_director,
                    ];
                }
            ),
        ];
    }
}
