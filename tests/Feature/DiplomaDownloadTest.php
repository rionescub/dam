<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Diploma;
use App\Models\Team;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Participants can download their own diploma as a PDF from the API,
 * and only their own.
 */
class DiplomaDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected string $storage;

    protected User $owner;

    protected Diploma $diploma;

    protected function setUp(): void
    {
        parent::setUp();

        // Render into a throwaway storage dir; no font cache dir exists yet,
        // so this also covers creating it before DomPDF needs it.
        $this->storage = sys_get_temp_dir() . '/diploma-test-' . uniqid();
        $this->app->useStoragePath($this->storage);
        config([
            'filesystems.disks.public.root' => $this->storage . '/app/public',
            'view.compiled' => $this->storage . '/framework/views',
            'dompdf.options.font_dir' => $this->storage . '/fonts',
            'dompdf.options.font_cache' => $this->storage . '/fonts',
        ]);
        File::ensureDirectoryExists($this->storage . '/framework/views');

        $team = Team::factory()->create(['link' => 'ro']);

        $this->owner = User::factory()->create(['current_team_id' => $team->id]);

        $contest = Contest::factory()->create(['team_id' => $team->id]);

        $work = Work::factory()->create([
            'contest_id' => $contest->id,
            'user_id' => $this->owner->id,
            'rank' => 1,
        ]);

        WorkDetails::create([
            'work_id' => $work->id,
            'full_name' => 'Ana Popescu',
            'city' => 'Galați',
            'school' => 'Școala Gimnazială nr. 1',
            'mentor' => 'Ion Ionescu',
            'type' => 'pictură',
            'age_group' => '10-14',
        ]);

        $this->diploma = Diploma::create([
            'user_id' => $this->owner->id,
            'contest_id' => $contest->id,
            'work_id' => $work->id,
            'description' => 'Diploma',
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->storage);

        parent::tearDown();
    }

    public function test_owner_can_download_their_diploma_as_pdf(): void
    {
        $response = $this->actingAs($this->owner)
            ->get("/api/diplomas/{$this->diploma->id}/download");

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', file_get_contents($response->baseResponse->getFile()->getPathname()));
    }

    public function test_other_users_cannot_download_the_diploma(): void
    {
        $other = User::factory()->create(['current_team_id' => $this->owner->current_team_id]);

        $this->actingAs($other)
            ->getJson("/api/diplomas/{$this->diploma->id}/download")
            ->assertForbidden();
    }
}
