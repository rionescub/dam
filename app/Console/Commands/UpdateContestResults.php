<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Models\Work;
use App\Models\Score;
use App\Models\Diploma;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateContestResults extends Command
{
    protected $signature = 'contest:update-results
                            {--team= : Only process contests of this team (its link, e.g. ro)}
                            {--contest= : Only process this contest ID, regardless of its ceremony date}';
    protected $description = 'Update contest results if the jury date has passed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $query = Contest::with(['works.scores', 'works.details']);

        if ($this->option('team') !== null) {
            $team = Team::where('link', $this->option('team'))->first();

            if (! $team) {
                $this->error("Team '{$this->option('team')}' not found.");

                return self::FAILURE;
            }

            $query->where('team_id', $team->id);
        }

        if ($this->option('contest') !== null) {
            $query->whereKey($this->option('contest'));
        } else {
            $query->whereDate('ceremony_date', Carbon::today());
        }

        $contests = $query->get();

        if ($contests->isEmpty()) {
            $this->warn('No matching contests found.');

            return self::SUCCESS;
        }

        foreach ($contests as $contest) {
            $this->line("Processing contest #{$contest->id}: {$contest->name}");

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
