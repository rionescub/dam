<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Score;
use App\Models\Team;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * contest:update-results ranks every contest whose ceremony is today, or only
 * one team's contests with --team, or one contest on any date with --contest.
 */
class UpdateContestResultsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Team $romania;

    protected Team $hungary;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create();

        $this->romania = Team::factory()->create(['link' => 'ro']);
        $this->hungary = Team::factory()->create(['link' => 'hu']);
    }

    protected function contestWithWork(Team $team, $ceremonyDate): Work
    {
        $contest = Contest::factory()->create([
            'team_id' => $team->id,
            'ceremony_date' => $ceremonyDate,
        ]);

        return Work::factory()->create(['contest_id' => $contest->id]);
    }

    /** The command gives every work it processes a diploma; rank alone defaults to 1. */
    protected function assertProcessed(Work $work): void
    {
        $this->assertDatabaseHas('diplomas', ['work_id' => $work->id]);
    }

    protected function assertNotProcessed(Work $work): void
    {
        $this->assertDatabaseMissing('diplomas', ['work_id' => $work->id]);
    }

    public function test_without_options_it_ranks_every_contest_with_a_ceremony_today(): void
    {
        $romanian = $this->contestWithWork($this->romania, today());
        $hungarian = $this->contestWithWork($this->hungary, today());
        $past = $this->contestWithWork($this->romania, today()->subWeek());

        $this->artisan('contest:update-results')->assertSuccessful();

        $this->assertProcessed($romanian);
        $this->assertProcessed($hungarian);
        $this->assertNotProcessed($past);
    }

    public function test_team_option_only_ranks_that_teams_contests(): void
    {
        $romanian = $this->contestWithWork($this->romania, today());
        $hungarian = $this->contestWithWork($this->hungary, today());

        $this->artisan('contest:update-results', ['--team' => 'ro'])->assertSuccessful();

        $this->assertProcessed($romanian);
        $this->assertNotProcessed($hungarian);
    }

    public function test_contest_option_ranks_that_contest_on_any_date(): void
    {
        $past = $this->contestWithWork($this->romania, today()->subWeek());
        $today = $this->contestWithWork($this->romania, today());

        $this->artisan('contest:update-results', ['--contest' => $past->contest_id])->assertSuccessful();

        $this->assertProcessed($past);
        $this->assertNotProcessed($today);
    }

    public function test_contest_option_ignores_a_contest_of_another_team(): void
    {
        $hungarian = $this->contestWithWork($this->hungary, today());

        $this->artisan('contest:update-results', ['--team' => 'ro', '--contest' => $hungarian->contest_id])
            ->expectsOutput('No matching contests found.')
            ->assertSuccessful();

        $this->assertNotProcessed($hungarian);
    }

    public function test_works_with_equal_scores_share_a_rank(): void
    {
        $contest = Contest::factory()->create(['team_id' => $this->romania->id, 'ceremony_date' => today()]);

        [$first, $tiedFirst, $third, $fourth] = collect([[10, 10, 10], [10, 10, 10], [5, 5, 5], [1, 1, 1]])
            ->map(function (array $criteria) use ($contest) {
                $work = Work::factory()->create(['contest_id' => $contest->id]);
                WorkDetails::create([
                    'work_id' => $work->id,
                    'full_name' => 'Participant',
                    'city' => 'City',
                    'type' => 'img',
                    'age_group' => '6-11',
                ]);
                Score::factory()->create([
                    'work_id' => $work->id,
                    'creativity_score' => $criteria[0],
                    'link_score' => $criteria[1],
                    'aesthetic_score' => $criteria[2],
                ]);

                return $work;
            });

        $this->artisan('contest:update-results')->assertSuccessful();

        $this->assertSame(1, $first->fresh()->rank);
        $this->assertSame(1, $tiedFirst->fresh()->rank);
        $this->assertSame(3, $third->fresh()->rank);
        $this->assertSame(4, $fourth->fresh()->rank);
    }

    public function test_unknown_team_fails(): void
    {
        $this->artisan('contest:update-results', ['--team' => 'xx'])
            ->expectsOutput("Team 'xx' not found.")
            ->assertFailed();
    }
}
