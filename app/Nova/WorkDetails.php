<?php

namespace App\Nova;

use App\Models\WorkDetails as WorkDetailsModel;
use App\Nova\Filters\ContestFilter;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class WorkDetails extends Resource
{
    /**
     * @var class-string<\App\Models\WorkDetails>
     */
    public static $model = WorkDetailsModel::class;

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
        $query->with(['work.user', 'work.contest']);

        $user = $request->user();

        if (! $user->is_admin()) {
            $query->whereHas('work.contest', function (Builder $contestQuery) use ($user): void {
                $contestQuery->where('team_id', $user->current_team_id);
            });
        }

        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        $query->with(['work.user', 'work.contest']);

        $user = $request->user();

        if (! $user->is_admin()) {
            $query->whereHas('work.contest', function (Builder $contestQuery) use ($user): void {
                $contestQuery->where('team_id', $user->current_team_id);
            });
        }

        return $query;
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Work'),

            Text::make('Full Name', 'full_name')
                ->sortable()
                ->rules('required', 'max:191'),

            Text::make('User Email', fn() => $this->work?->user?->email)
                ->sortable()
                ->onlyOnIndex(),

            Text::make('Country', 'country')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('County', 'county')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('City', 'city')
                ->sortable()
                ->rules('required', 'max:191'),

            Text::make('Mentor', 'mentor')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('Phone', 'phone')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('School', 'school')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('School Address', 'school_address')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('School Director', 'school_director')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Text::make('Year', 'year')
                ->sortable()
                ->rules('nullable', 'max:191'),

            Select::make('Age Group', 'age_group')
                ->sortable()
                ->options([
                    '6-11'  => '6–11',
                    '12-18' => '12–18',
                ])
                ->displayUsingLabels()
                ->rules('required', 'max:191'),

            Select::make('Type', 'type')
                ->sortable()
                ->options([
                    'video'   => 'Video',
                    'img'     => 'Image',
                    'artwork' => 'Artwork',
                ])
                ->displayUsingLabels()
                ->rules('required', 'max:191'),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new ContestFilter(throughWork: true),
        ];
    }
}
