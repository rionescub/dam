<?php

namespace Database\Seeders;

use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Contest;
use App\Models\Work;
use App\Models\Score;
use App\Models\Diploma;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('users')->insert([
            [
                'first_name' => 'Bogdan',
                'last_name' => 'Ionescu',
                'email' => 'office@developer-site.ro',
                'password' => Hash::make('Testing12345#'),
                'email_verified_at' => Carbon::now(),
            ]
        ]);

        // Create Users
        $users = User::factory(20)->create();

        // Create 5 Contests for the first user
        $firstUser = $users->first();
        $contests = Contest::factory(5)->create();


        // Create Works
        // create 1 work for each user except first and attribute them to a random contest
        foreach ($users as $user) {
            if ($user->id !== $firstUser->id) {
                $randomContest = $contests->random();
                $work = Work::factory()->create([
                    'user_id' => $user->id,
                    'contest_id' => $randomContest->id
                ]);

                // Assign the user to the contest
                $user->contests()->attach($randomContest->id);
            }
        }

        // Create Scores
        // create 1 score for each work
        Work::all()->each(function ($work) {
            Score::factory()->create([
                'user_id' => $work->user_id,
                'work_id' => $work->id,
            ]);
        });

        // Set rank for diploma based on total_score for each
        foreach ($contests as $contest) {
            // Join works with scores and order by total_score in descending order
            $works = $contest->works()
                ->join('scores', 'works.id', '=', 'scores.work_id')
                ->orderByDesc('scores.total_score')
                ->select('works.*', 'scores.total_score')
                ->get();

            // Update the rank for each work based on its position in the sorted list
            $works->each(function ($work, $index) {
                $work->update(['rank' => $index + 1]);
                $work->update(['total_score' => $work->scores->sum('total_score')]);
            });
        }


        // Create Diplomas
        // create 3 diplomas for the works/user combination in rank 1, 2 or 3 for each contest
        foreach ($contests as $contest) {
            $topWorks = $contest->works()->where('rank', '<=', 3)->get();
            foreach ($topWorks as $work) {
                Diploma::factory()->create([
                    'user_id' => $work->user_id,
                    'contest_id' => $work->contest_id,
                    'work_id' => $work->id,
                ]);
            }
        }
    }
}
