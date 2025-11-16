<?php

namespace App\Nova;

use App\Nova\Resource;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Text;
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
        $query->with(['contest', 'user', 'details']);

        $user = $request->user();

        if (!$user->is_admin()) {
            $query->whereHas('contest', function ($contestQuery) use ($user) {
                $contestQuery->where('team_id', $user->current_team_id);
            });
        }

        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        $query->with(['contest', 'user', 'details']);

        $user = $request->user();

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

            Text::make('Artwork Image', function () {
                if (!$this->file_path || !Storage::disk('public')->exists($this->file_path)) {
                    return '—';
                }

                $url = Storage::disk('public')->url($this->file_path);

                return '<img src="' . e($url) . '" alt="Artwork" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">';
            })->asHtml()->onlyOnIndex(),

            Image::make('Artwork Image', 'file_path')
                ->disk('public')
                ->thumbnail(function ($value, $disk) {
                    if (!$value || !Storage::disk($disk)->exists($value)) {
                        return null;
                    }

                    $mime = Storage::disk($disk)->mimeType($value) ?: 'image/jpeg';
                    $data = base64_encode(Storage::disk($disk)->get($value));

                    return 'data:' . $mime . ';base64,' . $data;
                })
                ->preview(function ($value, $disk) {
                    if (!$value || !Storage::disk($disk)->exists($value)) {
                        return null;
                    }

                    $mime = Storage::disk($disk)->mimeType($value) ?: 'image/jpeg';
                    $data = base64_encode(Storage::disk($disk)->get($value));

                    return 'data:' . $mime . ';base64,' . $data;
                })
                ->hideFromIndex(),

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

            Boolean::make('View on Front')
                ->sortable()
                ->onlyOnForms(),

            BelongsTo::make('Contest')
                ->sortable()
                ->rules('required'),

            BelongsTo::make('User', 'user', User::class)
                ->sortable()
                ->rules('required')
                ->searchable()
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->where('current_team_id', $request->user()->current_team_id);
                }),

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
            ExportAsCsv::make()
                ->withQuery(function ($query) {
                    $query->with(['user', 'contest', 'details']);

                    if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($query->getModel()))) {
                        $query->withoutTrashed();
                    }

                    return $query;
                })
                ->withFormat(
                    function ($model) {
                        return [
                            'id' => $model->getKey(),
                            'title' => $model->name ?? '',
                            'name' => $model->details?->full_name ?? $model->user?->name ?? '',
                            'file_path' => $model->file_path ? asset('storage/' . $model->file_path) : ($model->video_url ?? ''),
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
