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

            Text::make ('English Title', 'title_en')
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
                ->rules('required', 'integer', 'min:1'),

            Number::make('Total Score')
                ->readonly(),

            Boolean::make('Is Finalized')
                ->readonly() // Prevents this field from being edited manually in Nova
                ->sortable(),

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
                        'file_path' => $model->file_path, // Added file_path to CSV export
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
