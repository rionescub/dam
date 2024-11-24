<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContestAdded;
use Carbon\Carbon;

class NotifyNewContest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contest:notify-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification emails to users when a new contest is added';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get contests that have been added today
        $contests = Contest::whereDate('created_at', Carbon::today())->get();

        foreach ($contests as $contest) {
            // Get all users in the team of the contest
            $team = $contest->team; // Assuming Contest belongs to a Team
            $users = $team->users; // Assuming Team has a users relationship

            foreach ($users as $user) {
                // Send email to the user
                Mail::to($user->email)->send(new NewContestAdded($contest));
                $this->info("Email sent to {$user->email} for new Contest ID {$contest->id}");
            }
        }

        $this->info('All users have been notified for new contests.');

        return 0;
    }
}
