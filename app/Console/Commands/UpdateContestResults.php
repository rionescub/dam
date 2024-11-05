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
        // Get all contests where the jury_date has passed with eager loading of related works and scores
        $contests = Contest::with(['works.scores'])
            ->get();

        foreach ($contests as $contest) {
            // Get all works related to the contest
            $works = $contest->works;

            foreach ($works as $work) {
                // Sum total scores for each work
                $totalScore = $work->scores()->where('work_id', $work->id)->sum('total_score') ?: 3;

                // Update work total score
                $work->total_score = $totalScore;
                $work->save();
            }

            // Rank the works based on total scores
            $rankedWorks = $works->sortByDesc('total_score')->values();
            foreach ($rankedWorks as $index => $work) {
                $work->rank = $index + 1;
                $work->save();
            }

            // Generate diplomas for each work
            foreach ($works as $work) {
                Diploma::updateOrCreate(
                    [
                        'user_id' => $work->user_id,
                        'contest_id' => $contest->id,
                        'work_id' => $work->id,
                        'description' => $contest->description,
                    ],
                    []
                );
            }
        }

        $this->info('Contest results updated successfully.');
    }
}
