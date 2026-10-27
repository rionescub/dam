<?php

namespace App\Console\Commands;

use App\Services\InternationalContestSender;
use Illuminate\Console\Command;

class SendWinnersToInternationalContest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-to-international-contest
                            {--contest=* : Only send these contest IDs (repeat for several, e.g. --contest=18 --contest=24)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone all rank 1 winners (per type and age group, ties included) from finished contests into the active EN contest';

    /**
     * Execute the console command.
     */
    public function handle(InternationalContestSender $sender): int
    {
        if (! $sender->enTeam()) {
            $this->error('EN team (link = en) not found.');

            return Command::FAILURE;
        }

        $targetContest = $sender->targetContest();

        if (! $targetContest) {
            $this->error('No active contest (ceremony date after now) found for EN team.');

            return Command::FAILURE;
        }

        $this->info('Target EN contest ID: ' . $targetContest->id);

        $contestIds = $this->option('contest');
        $sourceContests = $sender->finishedContests($contestIds);

        foreach (array_diff($contestIds, $sourceContests->pluck('id')->map(fn ($id) => (string) $id)->all()) as $missingId) {
            $this->warn("Contest {$missingId} skipped: not a finished non-EN contest of this year.");
        }

        if ($sourceContests->isEmpty()) {
            $this->info('No finished contests found for the current year.');

            return Command::SUCCESS;
        }

        $totalCloned = 0;
        $totalSkipped = 0;

        foreach ($sourceContests as $contest) {
            $this->info('Processing contest ID: ' . $contest->id);

            if (! $sender->isRanked($contest)) {
                $this->warn('  Skipped: results not calculated yet. Run contest:update-results --contest=' . $contest->id . ' first.');
                continue;
            }

            $winnerCount = $sender->winners($contest)->count();

            if ($winnerCount === 0) {
                $this->info('  No rank 1 works found for this contest.');
                continue;
            }

            $clones = $sender->send($contest, $targetContest);

            foreach ($clones as $clonedWork) {
                $this->info(
                    sprintf(
                        '  Cloned work #%d (Age group: %s, Type: %s) to EN contest as work #%d',
                        $clonedWork->source_work_id,
                        $clonedWork->details->age_group,
                        $clonedWork->details->type,
                        $clonedWork->id
                    )
                );
            }

            $skipped = $winnerCount - $clones->count();
            $totalCloned += $clones->count();
            $totalSkipped += $skipped;

            $this->info('  Cloned ' . $clones->count() . ' category winners from this contest' . ($skipped ? ", {$skipped} already in the EN contest." : '.'));
        }

        $this->info('Cloning finished. Total cloned works: ' . $totalCloned . ', already cloned: ' . $totalSkipped . '.');

        return Command::SUCCESS;
    }
}
