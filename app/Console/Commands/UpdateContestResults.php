<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Models\Work;
use App\Models\Score;
use App\Models\Diploma;
use Carbon\Carbon;
use DB;

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
        // Get all contests with their works, scores, and details
        $contests = Contest::with(['works.scores', 'works.details'])
            ->where('jury_date', '<', Carbon::now())
            ->where('ceremony_date', '>=', Carbon::now())
            ->get();

        foreach ($contests as $contest) {
            // Get all works related to the contest
            $works = $contest->works;

            // Calculate total scores for each work
            foreach ($works as $work) {
                // Sum total scores for each work, defaulting to 3 if no scores are found
                $totalScore = $work->scores()->sum('total_score') ?: 3;


                // Update work total score
                $work->total_score = $totalScore;
                $work->save();
            }

            // Group works by work_details type and age_group
            $worksGrouped = $works->groupBy([
                function ($work) {
                    return $work->details->type;
                },
                function ($work) {
                    return $work->details->age_group;
                },
            ]);

            // Rank works within each type and age_group
            foreach ($worksGrouped as $type => $groupedByAgeGroup) {
                foreach ($groupedByAgeGroup as $ageGroup => $groupedWorks) {
                    // Sort the works in this group by total_score descending
                    $rankedWorks = $groupedWorks->sortByDesc('total_score')->values();

                    // Assign ranks within this group
                    foreach ($rankedWorks as $index => $work) {
                        $work->rank = $index + 1;
                        $work->save();
                    }
                }
            }

            // Generate diplomas for each work
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
