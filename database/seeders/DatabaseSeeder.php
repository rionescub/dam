<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;
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
        // Insert a specific user
        DB::table('users')->insert([
            [
                'first_name' => 'Bogdan',
                'last_name' => 'Ionescu',
                'email' => 'office@developer-site.ro',
                'role' => 'admin',
                'password' => Hash::make('Testing12345#'),
                'email_verified_at' => Carbon::now(),
            ]
        ]);

        DB::table('teams')->insert([
            [
                'name' => 'Romania',
                'link' => 'ro',
            ],
            [
                'name' => 'Hungary',
                'link' => 'hu',
            ],
            [
                'name' => 'Slovenia',
                'link' => 'sl',
            ],
        ]);

        // $teams = Team::all();
        // foreach ($teams as $team) {
        //     User::factory()->create([
        //         'email' => $team->language_code . '-organizer@developer-site.ro',
        //         'role' => 'organizer',
        //         'password' => Hash::make('Testing12345#'),
        //         'current_team_id' => $team->id,
        //     ]);
        //     User::factory()->create([
        //         'email' => $team->language_code . '-judge@developer-site.ro',
        //         'role' => 'judge',
        //         'password' => Hash::make('Testing12345#'),
        //         'current_team_id' => $team->id,
        //     ]);
        //     User::factory()->create([
        //         'email' => $team->language_code . '-contestant@developer-site.ro',
        //         'role' => 'contestant',
        //         'password' => Hash::make('Testing12345#'),
        //         'current_team_id' => $team->id,
        //     ]);
        // }

        // Assign each user to the correct team
        // $users = User::all();
        // foreach ($users as $user) {
        //     $user->teams()->attach($user->current_team_id);
        // }
        // // Assign the first user to all teams
        // $teamIds = Team::pluck('id');
        // $firstUser = User::first();
        // $firstUser->teams()->attach($teamIds);

        // // Create contests
        // $start = Carbon::tomorrow();
        // $end = Carbon::parse('2024-11-07');
        // $teams = Team::all();
        // foreach ($teams as $team) {
        //     $contest = Contest::factory()->create([
        //         'name' => 'Contest ' . $team->language_code,
        //         'team_id' => $team->id,
        //         'start_date' => $start,
        //         'end_date' => $end,
        //     ]);
        //     Work::factory(3)->create(['contest_id' => $contest->id]);
        // }

        // // Create teams
        // $teams = Team::factory(5)->create();
        // // Assign the first user to the first team
        // $team = $teams->first();
        // $user = User::where('email', 'office@developer-site.ro')->first();
        // $user->teams()->attach($team->id);
        // $user->current_team_id = $team->id;
        // $user->save();

        // // Create additional users and assign them to teams
        // $users = User::factory(20)->create();
        // foreach ($users as $user) {
        //     // Assign each user to a random team
        //     $team = $teams->random();
        //     $team->users()->attach($user->id);
        //     $user->current_team_id = $team->id;
        //     $user->save();
        // }

        // foreach ($teams as $team) {
        //     // For each team, create contests for users belonging to the team
        //     $usersInTeam = $team->users;
        //     foreach ($usersInTeam as $user) {
        //         Contest::factory()->create([
        //             'user_id' => $user->id,
        //             'team_id' => $team->id,
        //         ]);
        //     }
        // }

        // // Create works for each user and ensure that the contest belongs to the same team
        // foreach ($teams as $team) {
        //     $usersInTeam = $team->users;
        //     foreach ($usersInTeam as $user) {
        //         $contestsInTeam = Contest::where('team_id', $team->id)->get();
        //         $randomContest = $contestsInTeam->random();
        //         Work::factory()->create([
        //             'user_id' => $user->id,
        //             'contest_id' => $randomContest->id,
        //             'team_id' => $team->id, // Ensure the work belongs to the same team
        //         ]);
        //     }
        // }

        // // Create scores for works ensuring that all entities belong to the same team
        // foreach ($teams as $team) {
        //     $worksInTeam = Work::where('team_id', $team->id)->get();
        //     foreach ($worksInTeam as $work) {
        //         Score::factory()->create([
        //             'user_id' => $work->user_id,
        //             'work_id' => $work->id,
        //             'team_id' => $team->id, // Ensure the score belongs to the same team
        //         ]);
        //     }
        // }

        // // Assign ranks to works based on total score, ensuring they belong to the same team
        // foreach ($teams as $team) {
        //     $contestsInTeam = Contest::where('team_id', $team->id)->get();
        //     foreach ($contestsInTeam as $contest) {
        //         $works = $contest->works()
        //             ->join('scores', 'works.id', '=', 'scores.work_id')
        //             ->orderByDesc('scores.total_score')
        //             ->select('works.*', 'scores.total_score')
        //             ->get();

        //         // Assign rank to each work
        //         $works->each(function ($work, $index) {
        //             $work->update(['rank' => $index + 1]);
        //         });
        //     }
        // }

        // foreach ($teams as $team) {
        //     $contestsInTeam = Contest::where('team_id', $team->id)->get();
        //     foreach ($contestsInTeam as $contest) {
        //         $topWorks = $contest->works()->where('rank', '<=', 3)->get();
        //         foreach ($topWorks as $work) {
        //             Diploma::factory()->create([
        //                 'user_id' => $work->user_id,
        //                 'contest_id' => $work->contest_id,
        //                 'work_id' => $work->id,
        //                 'team_id' => $team->id,
        //             ]);
        //         }
        //     }
        // }
    }
}
