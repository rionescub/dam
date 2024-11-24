<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Models\Work;

class AddWorkToParentContest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contest:add-to-parent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach works with award_rank 1, 2, 3 to the parent contest if it exists';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Retrieve all contests
        $contests = Contest::with('parentContest')->get();

        foreach ($contests as $contest) {
            // Check if the contest has a parent
            if ($contest->parentContest) {
                // Get works with award_rank 1, 2, or 3
                $works = Work::where('contest_id', $contest->id)
                    ->whereIn('award_rank', [1, 2, 3])
                    ->get();

                foreach ($works as $work) {
                    // Update the contest_id to parent contest id
                    $work->contest_id = $contest->parentContest->id;
                    $work->save();

                    $this->info("Work ID {$work->id} attached to Parent Contest ID {$contest->parentContest->id}");
                }
            }
        }

        $this->info('All eligible works have been attached to their parent contests if applicable.');

        return 0;
    }
}
