<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Models\Work;
use App\Models\Score;
use App\Models\Diploma;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateContestResults extends Command
{
    protected $signature = 'contest:update-results';
    protected $description = 'Update contest results if the jury date has passed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $contests = Contest::with(['works.scores', 'works.details'])
            ->whereDate('ceremony_date', Carbon::today())
            ->get();

        foreach ($contests as $contest) {
            $works = $contest->works()->withoutTrashed()->get();

            foreach ($works as $work) {
                $totalScore = $work->scores()->sum('total_score') ?: 3;
                $work->total_score = $totalScore;
                $work->save();
            }

            $worksGrouped = $works->groupBy([
                function ($work) {
                    return optional($work->details)->type ?? 'undefined';
                },
                function ($work) {
                    return optional($work->details)->age_group ?? 'undefined';
                },
            ]);

            foreach ($worksGrouped as $type => $groupedByAgeGroup) {
                foreach ($groupedByAgeGroup as $ageGroup => $groupedWorks) {
                    Log::info('Ranking group', [
                        'contest_id' => $contest->id,
                        'type' => $type,
                        'age_group' => $ageGroup,
                        'works_count' => $groupedWorks->count(),
                        'work_ids' => $groupedWorks->pluck('id'),
                        'scores' => $groupedWorks->pluck('total_score', 'id'),
                    ]);

                    $rankedWorks = $groupedWorks->sortByDesc('total_score')->values();

                    foreach ($rankedWorks as $index => $work) {
                        $work->rank = $index + 1;
                        $work->save();
                        Log::info('Assigned rank', [
                            'work_id' => $work->id,
                            'rank' => $work->rank,
                            'total_score' => $work->total_score,
                            'type' => $type,
                            'age_group' => $ageGroup,
                        ]);
                    }
                }
            }

            foreach ($works as $work) {
                Diploma::updateOrCreate(
                    [
                        'user_id'     => $work->user_id,
                        'contest_id'  => $contest->id,
                        'work_id'     => $work->id,
                        'description' => $contest->description,
                    ],
                    []
                );
            }
        }

        $this->info('Contest results updated successfully.');
    }
}
