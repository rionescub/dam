<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Team;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * app:send-to-international-contest clones every rank 1 work of finished
 * national contests into the open EN contest, once.
 */
class SendWinnersToInternationalContestTest extends TestCase
{
    use RefreshDatabase;

    protected Contest $international;

    protected Contest $national;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create();

        $this->international = Contest::factory()->create([
            'team_id' => Team::factory()->create(['link' => 'en'])->id,
            'ceremony_date' => now()->addMonth(),
        ]);

        $this->national = Contest::factory()->create([
            'team_id' => Team::factory()->create(['link' => 'ro'])->id,
            'start_date' => now()->startOfYear(),
            'ceremony_date' => now()->subDay(),
        ]);
    }

    protected function work(int $rank, string $type = 'photo', string $ageGroup = '6-10'): Work
    {
        $work = Work::factory()->create(['contest_id' => $this->national->id, 'rank' => $rank]);

        WorkDetails::create([
            'work_id' => $work->id,
            'full_name' => 'Participant',
            'city' => 'City',
            'type' => $type,
            'age_group' => $ageGroup,
        ]);

        return $work;
    }

    protected function clonesOf(Work $work)
    {
        return Work::withTrashed()
            ->where('contest_id', $this->international->id)
            ->where('source_work_id', $work->id)
            ->get();
    }

    public function test_it_sends_every_work_tied_for_first_place_in_a_category(): void
    {
        $first = $this->work(1);
        $tiedFirst = $this->work(1);
        $second = $this->work(2);
        $otherCategory = $this->work(1, 'video');

        $this->artisan('app:send-to-international-contest')->assertSuccessful();

        $this->assertCount(1, $this->clonesOf($first));
        $this->assertCount(1, $this->clonesOf($tiedFirst));
        $this->assertCount(1, $this->clonesOf($otherCategory));
        $this->assertCount(0, $this->clonesOf($second));
        $this->assertSame('photo', $this->clonesOf($first)->first()->details->type);
    }

    public function test_running_it_again_does_not_duplicate_works(): void
    {
        $winner = $this->work(1);

        $this->artisan('app:send-to-international-contest')->assertSuccessful();
        $this->artisan('app:send-to-international-contest')->assertSuccessful();

        $this->assertCount(1, $this->clonesOf($winner));
        $this->assertSame(1, Work::where('contest_id', $this->international->id)->count());
    }

    public function test_a_deleted_clone_is_not_sent_again(): void
    {
        $winner = $this->work(1);

        $this->artisan('app:send-to-international-contest')->assertSuccessful();
        $this->clonesOf($winner)->first()->delete();
        $this->artisan('app:send-to-international-contest')->assertSuccessful();

        $this->assertCount(1, $this->clonesOf($winner));
    }
}
