<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Sponsor;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The default (non-Romanian) diploma shows the logos of the sponsors the
 * contest's team added in Nova, not Romania's hard-coded ones.
 */
class DiplomaSponsorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_diploma_shows_only_the_contest_teams_sponsors(): void
    {
        $views = sys_get_temp_dir() . '/diploma-views-' . uniqid();
        File::ensureDirectoryExists($views);
        config(['view.compiled' => $views]);

        $disk = Storage::fake(config('nova.storage_disk', 'public'));

        $ro = Team::factory()->create(['link' => 'ro']);
        $hu = Team::factory()->create(['link' => 'hu']);

        $huSponsor = $this->sponsor($hu, 'hu-sponsor.png');
        $roSponsor = $this->sponsor($ro, 'ro-sponsor.png');
        $this->sponsor($hu, 'missing-file.png', upload: false);

        $this->actingAs(User::factory()->create(['current_team_id' => $hu->id]));
        $contest = Contest::factory()->create(['team_id' => $hu->id]);

        $diploma = (object) [
            'contest' => $contest,
            'work' => (object) [
                'name' => 'Mű',
                'rank' => 1,
                'award_rank' => null,
                'details' => (object) [
                    'full_name' => 'Kovács Anna',
                    'school' => 'Iskola',
                    'mentor' => 'Tanár',
                    'age_group' => '10-14',
                    'type' => 'festmény',
                ],
            ],
        ];

        $html = view('pdf.diploma', compact('diploma'))->render();

        File::deleteDirectory($views);

        $this->assertStringContainsString($disk->path($huSponsor->image), $html);
        $this->assertStringNotContainsString($disk->path($roSponsor->image), $html);
        $this->assertStringNotContainsString('missing-file.png', $html);
        foreach (['ipcdr.png', 'erste.svg', 'gwp.png', 'mmap.png', 'gwp.jpg'] as $romanianLogo) {
            $this->assertStringNotContainsString($romanianLogo, $html);
        }
    }

    protected function sponsor(Team $team, string $file, bool $upload = true): Sponsor
    {
        $path = 'sponsors/' . $file;

        if ($upload) {
            Storage::disk(config('nova.storage_disk', 'public'))
                ->putFileAs('sponsors', UploadedFile::fake()->image($file, 300, 100), $file);
        }

        $sponsor = new Sponsor(['name' => $file, 'image' => $path, 'type' => 'sponsor']);
        $sponsor->team_id = $team->id;
        $sponsor->saveQuietly();

        return $sponsor;
    }
}
