<?php

namespace App\Console\Commands;

use App\Models\Contest;
use App\Models\Team;
use App\Models\Work;
use App\Models\WorkDetails;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWinnersToInternationalContest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-to-international-contest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone rank 1 winners (per type and age group) from finished contests into the active EN contest';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $currentYear = (int) $now->year;

        $enTeam = Team::where('link', 'en')->first();

        if (! $enTeam) {
            $this->error('EN team (link = en) not found.');

            return Command::FAILURE;
        }

        $targetContest = Contest::where('team_id', $enTeam->id)
            ->where('ceremony_date', '>', $now)
            ->orderBy('ceremony_date')
            ->first();

        if (! $targetContest) {
            $this->error('No active contest (ceremony date after now) found for EN team.');

            return Command::FAILURE;
        }

        $this->info('Target EN contest ID: ' . $targetContest->id);

        $sourceContests = Contest::whereYear('start_date', $currentYear)
            ->where('ceremony_date', '<', $now)
            ->where('team_id', '!=', $enTeam->id)
            ->get();

        if ($sourceContests->isEmpty()) {
            $this->info('No finished contests found for the current year.');

            return Command::SUCCESS;
        }

        $totalCloned = 0;

        foreach ($sourceContests as $contest) {
            $this->info('Processing contest ID: ' . $contest->id);

            $works = Work::with('details')
                ->where('contest_id', $contest->id)
                ->whereHas('details', function ($query): void {
                    $query->where('rank', 1);
                })
                ->get();

            if ($works->isEmpty()) {
                $this->info('  No rank 1 works found for this contest.');
                continue;
            }

            $categoriesSeen = [];
            $clonedForContest = 0;

            foreach ($works as $work) {
                /** @var WorkDetails|null $details */
                $details = $work->details;

                if (! $details) {
                    continue;
                }

                $type = (string) $details->type;
                $ageGroup = (string) $details->age_group;
                $categoryKey = $ageGroup . '||' . $type;

                if (isset($categoriesSeen[$categoryKey])) {
                    continue;
                }

                $categoriesSeen[$categoryKey] = true;

                $clonedWork = $work->replicate();
                $clonedWork->contest_id = $targetContest->id;
                $clonedWork->created_at = $now;
                $clonedWork->updated_at = $now;
                $clonedWork->push();

                $clonedDetails = $details->replicate();
                $clonedDetails->work_id = $clonedWork->id;
                $clonedDetails->created_at = $now;
                $clonedDetails->updated_at = $now;
                $clonedDetails->push();

                $clonedForContest++;
                $totalCloned++;

                $this->info(
                    sprintf(
                        '  Cloned work #%d (Age group: %s, Type: %s) to EN contest as work #%d',
                        $work->id,
                        $ageGroup,
                        $type,
                        $clonedWork->id
                    )
                );
            }

            if ($clonedForContest === 0) {
                $this->info('  No unique category winners cloned for this contest.');
            } else {
                $this->info('  Cloned ' . $clonedForContest . ' category winners from this contest.');
            }
        }

        $this->info('Cloning finished. Total cloned works: ' . $totalCloned . '.');

        return Command::SUCCESS;
    }
}
