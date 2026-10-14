<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Team;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The English title and description are optional on update: sending them
 * saves them, leaving them out keeps what is already stored.
 */
class WorkUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Work $work;

    protected function setUp(): void
    {
        parent::setUp();

        $team = Team::factory()->create(['link' => 'ro']);

        $this->owner = User::factory()->create(['current_team_id' => $team->id]);

        $contest = Contest::factory()->create(['team_id' => $team->id]);

        $this->work = Work::factory()->create([
            'contest_id' => $contest->id,
            'user_id' => $this->owner->id,
            'title_en' => 'Old English title',
            'description_en' => 'Old English description',
        ]);
    }

    public function test_update_saves_the_english_title_and_description(): void
    {
        $this->actingAs($this->owner)
            ->putJson("/api/works/{$this->work->id}", [
                'title' => 'Titlu nou',
                'title_en' => 'New English title',
                'description' => 'Descriere noua',
                'description_en' => 'New English description',
            ])
            ->assertOk();

        $this->work->refresh();

        $this->assertSame('Titlu nou', $this->work->name);
        $this->assertSame('New English title', $this->work->title_en);
        $this->assertSame('Descriere noua', $this->work->description);
        $this->assertSame('New English description', $this->work->description_en);
    }

    public function test_update_keeps_the_english_fields_when_they_are_not_sent(): void
    {
        $this->actingAs($this->owner)
            ->putJson("/api/works/{$this->work->id}", [
                'title' => 'Titlu nou',
                'description' => 'Descriere noua',
            ])
            ->assertOk();

        $this->work->refresh();

        $this->assertSame('Old English title', $this->work->title_en);
        $this->assertSame('Old English description', $this->work->description_en);
    }

    public function test_update_rejects_an_english_title_that_is_too_long(): void
    {
        $this->actingAs($this->owner)
            ->putJson("/api/works/{$this->work->id}", [
                'title' => 'Titlu nou',
                'title_en' => str_repeat('a', 256),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title_en');
    }
}
