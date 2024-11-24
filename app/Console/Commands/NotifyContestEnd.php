<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContestEnded;
use Carbon\Carbon;

class NotifyContestEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contest:notify-end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification emails to users when a contest ends';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get contests that have just ended today
        $contests = Contest::whereDate('end_date', Carbon::today())->get();

        foreach ($contests as $contest) {
            // Get all users in the contest
            $users = $contest->users; // Assuming you have a relationship defined on the Contest model

            foreach ($users as $user) {
                // Send email to the user
                Mail::to($user->email)->send(new ContestEnded($contest));
                $this->info("Email sent to {$user->email} for Contest ID {$contest->id}");
            }
        }

        $this->info('All users have been notified for ended contests.');

        return 0;
    }
}
