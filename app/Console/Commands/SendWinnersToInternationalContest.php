<?php

namespace App\Console\Commands;

use App\Models\Contest;
use App\Models\Diploma;
use App\Models\Team;
use App\Models\Work;
use App\Models\WorkDetails;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Clone all rank 1 winners (per type and age group, ties included) from finished contests into the active EN contest';

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
        $totalSkipped = 0;

        foreach ($sourceContests as $contest) {
            $this->info('Processing contest ID: ' . $contest->id);

            // contest:update-results gives every work a diploma; without them every work is still on the default rank 1
            if (! Diploma::where('contest_id', $contest->id)->exists()) {
                $this->warn('  Skipped: results not calculated yet. Run contest:update-results --contest=' . $contest->id . ' first.');
                continue;
            }

            // Every rank 1 work is sent, so works tied for first place in a category all go
            $works = Work::with('details')
                ->where('contest_id', $contest->id)
                ->where('rank', 1)
                ->has('details')
                ->get();

            if ($works->isEmpty()) {
                $this->info('  No rank 1 works found for this contest.');
                continue;
            }

            $clonedForContest = 0;

            foreach ($works as $work) {
                /** @var WorkDetails $details */
                $details = $work->details;

                // Includes deleted clones, so a winner removed from the EN contest isn't sent again
                $alreadyCloned = Work::withTrashed()
                    ->where('contest_id', $targetContest->id)
                    ->where('source_work_id', $work->id)
                    ->exists();

                if ($alreadyCloned) {
                    $this->info(sprintf('  Skipped work #%d: already in the EN contest', $work->id));
                    $totalSkipped++;
                    continue;
                }

                $clonedWork = DB::transaction(function () use ($work, $details, $targetContest, $now): Work {
                    $clonedWork = $work->replicate();
                    $clonedWork->contest_id = $targetContest->id;
                    $clonedWork->source_work_id = $work->id;
                    $clonedWork->created_at = $now;
                    $clonedWork->updated_at = $now;
                    $clonedWork->save();

                    $clonedDetails = $details->replicate();
                    $clonedDetails->work_id = $clonedWork->id;
                    $clonedDetails->created_at = $now;
                    $clonedDetails->updated_at = $now;
                    $clonedDetails->save();

                    return $clonedWork;
                });

                $clonedForContest++;
                $totalCloned++;

                $this->info(
                    sprintf(
                        '  Cloned work #%d (Age group: %s, Type: %s) to EN contest as work #%d',
                        $work->id,
                        $details->age_group,
                        $details->type,
                        $clonedWork->id
                    )
                );
            }

            $this->info('  Cloned ' . $clonedForContest . ' category winners from this contest.');
        }

        $this->info('Cloning finished. Total cloned works: ' . $totalCloned . ', already cloned: ' . $totalSkipped . '.');

        return Command::SUCCESS;
    }
}
