<?php

namespace App\Nova;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Http\Requests\NovaRequest;

class Work extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Work>
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $user = $request->user();

        // If the user is not an admin, filter by the contests they have access to
        if (!$user->is_admin()) {
            $query->whereHas('contest', function ($contestQuery) use ($user) {
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

        // If the user is not an admin, filter by the contests they have access to
        if (!$user->is_admin()) {
            $query->whereHas('contest', function ($contestQuery) use ($user) {
                $contestQuery->where('team_id', $user->current_team_id);
            });
        }

        return $query;
    }
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
        'name',
        'description',
        'title_en',
        'description_en',
        'video_url',
        'file_path',

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

            Text::make('Title', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('English Title', 'title_en')
                ->sortable()
                ->rules('required', 'max:255'),

            Textarea::make('Description')
                ->rules('required'),

            Textarea::make('English Description', 'description_en')
                ->rules('required'),

            Text::make('Video URL')
                ->nullable(),

            File::make('File', 'file_path')
                ->disk('public')
                ->nullable(), // Field to handle artwork uploads

            Number::make('Rank')
                ->min(1)
                ->readonly()
                ->sortable()
                ->rules('required', 'integer', 'min:1'),

            Number::make('Award Rank')
                ->min(1)
                ->sortable()
                ->nullable(),


            Number::make('Total Score')
                ->sortable()
                ->readonly(),

            // Boolean::make('Is Finalized')
            //     ->readonly() // Prevents this field from being edited manually in Nova
            //     ->sortable(),
            Boolean::make('View on Front')
                ->sortable(),

            BelongsTo::make('Contest')
                ->sortable()
                ->rules('required'),

            BelongsTo::make('User')
                ->sortable()
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
                        'title' => $model->name ?? '',
                        'name' => $model->details?->full_name ?? $model->user?->name ?? '',
                        'file_path' => $model->file_path ? asset("storage/" . $model->file_path) : ($model->video_url ?? ''),
                        'description' => $model->description ?? '',
                        'rank' => $model->rank ?? '',
                        'total_score' => $model->total_score ?? '',
                        'contest_name' => $model->contest?->name ?? '',
                        'user_name' => $model->user?->name ?? '',
                        'type' => $model->details?->type ?? '',
                        'age_group' => $model->details?->age_group ?? '',
                        'year' => $model->details?->year ?? '',
                        'country' => $model->details?->country ?? '',
                        'county' => $model->details?->county ?? '',
                        'city' => $model->details?->city ?? '',
                        'work_details_contact' => implode("\n", array_filter([
                            'Teacher name: ' . ($model->details?->mentor ?? '') . "\n",
                            'Email: ' . ($model->user->email ?? '') . "\n",
                            'Phone: ' . ($model->details?->phone ?? '') . "\n",
                            'School: ' . ($model->details?->school ?? '') . "\n",
                        ])),
                    ];
                }
            ),
        ];
    }
}
