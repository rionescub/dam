<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Diploma;
use App\Models\Team;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkDetails;
use App\Nova\Actions\SendToInternationalContest;
use App\Nova\Filters\SentFromContestFilter;
use App\Nova\Work as NovaWork;
use App\Services\InternationalContestSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Filters\FilterDecoder;
use Laravel\Nova\Http\Requests\NovaRequest;
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

    /** A work in a ranked contest: contest:update-results gives each work a diploma. */
    protected function work(int $rank, string $type = 'photo', string $ageGroup = '6-10', bool $ranked = true, ?Contest $contest = null): Work
    {
        $work = Work::factory()->create(['contest_id' => ($contest ?? $this->national)->id, 'rank' => $rank]);

        if ($ranked) {
            Diploma::create([
                'user_id' => $work->user_id,
                'contest_id' => $work->contest_id,
                'work_id' => $work->id,
                'description' => 'Diploma',
            ]);
        }

        WorkDetails::create([
            'work_id' => $work->id,
            'full_name' => 'Participant',
            'city' => 'City',
            'type' => $type,
            'age_group' => $ageGroup,
        ]);

        return $work;
    }

    protected function finishedNationalContest(): Contest
    {
        return Contest::factory()->create([
            'team_id' => $this->national->team_id,
            'start_date' => now()->startOfYear(),
            'ceremony_date' => now()->subDay(),
        ]);
    }

    protected function canSend(Contest $contest): bool
    {
        // A fresh sender, as each Nova request gets
        return (new InternationalContestSender)->canSend($contest);
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

    public function test_it_skips_a_contest_whose_results_were_not_calculated(): void
    {
        // Unranked works all keep the default rank 1
        $unranked = $this->work(1, ranked: false);
        $alsoUnranked = $this->work(1, ranked: false);

        $this->artisan('app:send-to-international-contest')
            ->expectsOutput('  Skipped: results not calculated yet. Run contest:update-results --contest='.$this->national->id.' first.')
            ->assertSuccessful();

        $this->assertCount(0, $this->clonesOf($unranked));
        $this->assertCount(0, $this->clonesOf($alsoUnranked));
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

    public function test_contest_option_only_sends_those_contests(): void
    {
        $second = $this->finishedNationalContest();
        $third = $this->finishedNationalContest();

        $fromFirst = $this->work(1);
        $fromSecond = $this->work(1, contest: $second);
        $fromThird = $this->work(1, contest: $third);

        $this->artisan('app:send-to-international-contest', ['--contest' => [$this->national->id, $second->id]])
            ->assertSuccessful();

        $this->assertCount(1, $this->clonesOf($fromFirst));
        $this->assertCount(1, $this->clonesOf($fromSecond));
        $this->assertCount(0, $this->clonesOf($fromThird));
    }

    public function test_contest_option_warns_about_a_contest_that_cannot_be_sent(): void
    {
        $this->artisan('app:send-to-international-contest', ['--contest' => [$this->international->id]])
            ->expectsOutput("Contest {$this->international->id} skipped: not a finished non-EN contest of this year.")
            ->assertSuccessful();
    }

    public function test_a_ranked_finished_contest_with_unsent_winners_can_be_sent(): void
    {
        $this->work(1);

        $this->assertTrue($this->canSend($this->national));
    }

    public function test_the_international_contest_itself_cannot_be_sent(): void
    {
        $this->international->forceFill(['ceremony_date' => now()->subDay(), 'start_date' => now()->startOfYear()])->save();
        Contest::factory()->create(['team_id' => $this->international->team_id, 'ceremony_date' => now()->addMonth()]);
        $this->work(1, contest: $this->international);

        $this->assertFalse($this->canSend($this->international));
    }

    public function test_a_contest_cannot_be_sent_before_its_ceremony_or_ranking(): void
    {
        $this->work(1, ranked: false);
        $this->assertFalse($this->canSend($this->national), 'unranked');

        $running = Contest::factory()->create([
            'team_id' => $this->national->team_id,
            'start_date' => now()->startOfYear(),
            'ceremony_date' => now()->addWeek(),
        ]);
        $this->work(1, contest: $running);
        $this->assertFalse($this->canSend($running), 'ceremony not reached');

        $lastYear = Contest::factory()->create([
            'team_id' => $this->national->team_id,
            'start_date' => now()->subYear()->startOfYear(),
            'ceremony_date' => now()->subYear(),
        ]);
        $this->work(1, contest: $lastYear);
        $this->assertFalse($this->canSend($lastYear), 'last year');
    }

    public function test_a_contest_cannot_be_sent_twice(): void
    {
        $this->work(1);
        $this->artisan('app:send-to-international-contest')->assertSuccessful();

        $this->assertFalse($this->canSend($this->national));
    }

    public function test_nothing_can_be_sent_without_an_open_international_contest(): void
    {
        $this->work(1);
        $this->international->forceFill(['ceremony_date' => now()->subDay()])->save();

        $this->assertFalse($this->canSend($this->national));
    }

    public function test_the_nova_action_sends_the_contests_winners(): void
    {
        $first = $this->work(1);
        $tiedFirst = $this->work(1);
        $second = $this->work(2);

        $response = (new SendToInternationalContest)->handle(
            new ActionFields(collect(), collect()),
            collect([$this->national])
        );

        $this->assertSame("Sent 2 winning works to {$this->international->name}.", $response['message']);
        $this->assertSame('/resources/works', $response['visit']['path']);
        $this->assertSame(
            ['works_filter' => SendToInternationalContest::sentFromFilter($this->national->id)],
            $response['visit']['options']
        );
        $this->assertCount(1, $this->clonesOf($first));
        $this->assertCount(1, $this->clonesOf($tiedFirst));
        $this->assertCount(0, $this->clonesOf($second));
    }

    public function test_the_nova_action_opens_works_filtered_to_what_was_sent(): void
    {
        $winner = $this->work(1);
        $other = $this->finishedNationalContest();
        $otherWinner = $this->work(1, contest: $other);
        $this->artisan('app:send-to-international-contest', ['--contest' => [$other->id]])->assertSuccessful();

        (new SendToInternationalContest)->handle(new ActionFields(collect(), collect()), collect([$this->national]));

        // Nova decodes works_filter this way on the Works index
        $filters = (new FilterDecoder(
            SendToInternationalContest::sentFromFilter($this->national->id),
            [new SentFromContestFilter]
        ))->filters();
        $this->assertCount(1, $filters);

        $shown = $filters->first()->__invoke(NovaRequest::create('/'), Work::query())->pluck('id');

        $this->assertEquals($this->clonesOf($winner)->pluck('id'), $shown);
        $this->assertNotContains($this->clonesOf($otherWinner)->first()->id, $shown);
    }

    public function test_sent_from_filter_lists_only_contests_that_sent_works(): void
    {
        $this->work(1);
        $notSent = $this->finishedNationalContest();
        $this->work(1, ranked: false, contest: $notSent);

        $this->artisan('app:send-to-international-contest')->assertSuccessful();

        $this->assertSame(
            [$this->national->name => $this->national->id],
            (new SentFromContestFilter)->options(NovaRequest::create('/'))
        );
    }

    public function test_sent_from_field_shows_the_source_contest_and_work(): void
    {
        $winner = $this->work(1);
        $this->artisan('app:send-to-international-contest')->assertSuccessful();
        $clone = $this->clonesOf($winner)->first();

        $this->assertSame("{$this->national->name} (work #{$winner->id})", $this->sentFromField($clone));
        $this->assertNull($this->sentFromField($winner), 'national works were not sent from anywhere');
    }

    protected function sentFromField(Work $work): ?string
    {
        $field = collect((new NovaWork($work))->fields(NovaRequest::create('/')))
            ->first(fn ($field) => $field->name === 'Sent from');

        $field->resolveForDisplay($work);

        return $field->value;
    }
}
