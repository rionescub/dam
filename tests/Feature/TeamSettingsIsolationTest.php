<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamSetting;
use App\Models\User;
use App\Repositories\TeamSettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Settings live in a single `nova_settings` table shared by every team, while
 * the nova-settings package queries them by `key` alone. These tests pin down
 * that one team's edits never reach another team's rows.
 */
class TeamSettingsIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Team $romania;

    protected Team $hungary;

    protected User $hungarianAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->romania = Team::factory()->create(['name' => 'Romania', 'link' => 'ro']);
        $this->hungary = Team::factory()->create(['name' => 'Hungary', 'link' => 'hu']);

        $this->hungarianAdmin = User::factory()->create([
            'role' => 'admin',
            'current_team_id' => $this->hungary->id,
        ]);
    }

    /** The row inserted first is the one the package would have picked up. */
    protected function seedSetting(Team $team, ?string $group, string $key, $value): void
    {
        DB::table('nova_settings')->insert([
            'team_id' => $team->id,
            'group' => $group,
            'key' => $key,
            'value' => $value,
        ]);
    }

    protected function settingValue(Team $team, string $key)
    {
        return DB::table('nova_settings')
            ->where('team_id', $team->id)
            ->where('key', $key)
            ->value('value');
    }

    public function test_uploading_a_file_setting_leaves_other_teams_alone(): void
    {
        Storage::fake('public');

        $this->seedSetting($this->romania, 'rules', 'rules_pdf_file', 'romanian-rulebook.pdf');
        $this->seedSetting($this->hungary, 'rules', 'rules_pdf_file', 'old-hungarian-rulebook.pdf');

        $this->actingAs($this->hungarianAdmin)
            ->post('/nova-vendor/nova-settings/settings', [
                'path' => 'rules',
                'rules_pdf_file' => UploadedFile::fake()->create('hungarian-rulebook.pdf', 16, 'application/pdf'),
            ])
            ->assertSuccessful();

        $this->assertSame(
            'romanian-rulebook.pdf',
            $this->settingValue($this->romania, 'rules_pdf_file'),
            'Uploading on the Hungarian page overwrote the Romanian rulebook.'
        );

        $hungarian = $this->settingValue($this->hungary, 'rules_pdf_file');
        $this->assertNotSame('old-hungarian-rulebook.pdf', $hungarian);
        Storage::disk('public')->assertExists($hungarian);
    }

    public function test_uploading_an_image_setting_leaves_other_teams_alone(): void
    {
        Storage::fake('public');

        $this->seedSetting($this->romania, 'about', 'about_banner_image', 'moldova-banner.jpg');

        $this->actingAs($this->hungarianAdmin)
            ->post('/nova-vendor/nova-settings/settings', [
                'path' => 'about',
                'about_banner_image' => UploadedFile::fake()->image('hungarian-banner.jpg'),
            ])
            ->assertSuccessful();

        $this->assertSame('moldova-banner.jpg', $this->settingValue($this->romania, 'about_banner_image'));
        $this->assertNotNull($this->settingValue($this->hungary, 'about_banner_image'));
    }

    public function test_a_file_upload_does_not_create_a_row_without_a_team(): void
    {
        Storage::fake('public');

        $this->actingAs($this->hungarianAdmin)
            ->post('/nova-vendor/nova-settings/settings', [
                'path' => 'terms',
                'terms_pdf_file' => UploadedFile::fake()->create('terms.pdf', 8, 'application/pdf'),
            ])
            ->assertSuccessful();

        $this->assertSame(0, DB::table('nova_settings')->whereNull('team_id')->count());
        $this->assertSame(
            1,
            DB::table('nova_settings')->where('key', 'terms_pdf_file')->count(),
            'The setting should exist exactly once, for the team that uploaded it.'
        );
        $this->assertSame('terms', DB::table('nova_settings')->where('key', 'terms_pdf_file')->value('group'));
    }

    public function test_text_settings_are_written_to_the_current_team_only(): void
    {
        $this->seedSetting($this->romania, 'contact', 'email_1', 'contact@ro.example');

        $this->actingAs($this->hungarianAdmin)
            ->post('/nova-vendor/nova-settings/settings', [
                'path' => 'contact',
                'email_1' => 'contact@hu.example',
            ])
            ->assertSuccessful();

        $this->assertSame('contact@ro.example', $this->settingValue($this->romania, 'email_1'));
        $this->assertSame('contact@hu.example', $this->settingValue($this->hungary, 'email_1'));
    }

    public function test_deleting_a_file_setting_clears_only_the_current_teams_row(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('hungarian-rulebook.pdf', 'hu');
        Storage::disk('public')->put('romanian-rulebook.pdf', 'ro');

        $this->seedSetting($this->romania, 'rules', 'rules_pdf_file', 'romanian-rulebook.pdf');
        $this->seedSetting($this->hungary, 'rules', 'rules_pdf_file', 'hungarian-rulebook.pdf');

        $this->actingAs($this->hungarianAdmin)
            ->delete('/nova-api/nova-settings/rules/field/rules_pdf_file')
            ->assertSuccessful();

        $this->assertSame('romanian-rulebook.pdf', $this->settingValue($this->romania, 'rules_pdf_file'));
        $this->assertNull($this->settingValue($this->hungary, 'rules_pdf_file'));

        Storage::disk('public')->assertExists('romanian-rulebook.pdf');
        Storage::disk('public')->assertMissing('hungarian-rulebook.pdf');
    }

    public function test_the_settings_model_only_sees_and_saves_the_current_teams_rows(): void
    {
        $this->seedSetting($this->romania, 'rules', 'rules_title', 'Regulament');
        $this->seedSetting($this->hungary, 'rules', 'rules_title', 'Szabályzat');

        $this->actingAs($this->hungarianAdmin);

        $this->assertSame(1, TeamSetting::where('key', 'rules_title')->count());

        $setting = TeamSetting::where('key', 'rules_title')->first();
        $this->assertSame('Szabályzat', $setting->value);

        $setting->value = 'Új szabályzat';
        $setting->save();

        $this->assertSame('Regulament', $this->settingValue($this->romania, 'rules_title'));
        $this->assertSame('Új szabályzat', $this->settingValue($this->hungary, 'rules_title'));
    }

    public function test_the_settings_model_returns_nothing_without_a_team_context(): void
    {
        $this->seedSetting($this->romania, 'rules', 'rules_title', 'Regulament');

        $this->assertSame(0, TeamSetting::count());
    }

    public function test_the_repository_repairs_a_row_whose_group_was_lost(): void
    {
        // Rows the package created on its own have no group, which hid them from
        // the site and made a second insert collide on the (team_id, key) index.
        $this->seedSetting($this->hungary, null, 'rules_title', 'Szabályzat');

        $repository = new TeamSettingsRepository($this->hungary->id, 'rules');

        $this->assertSame('Szabályzat', $repository->getSetting('rules_title'));

        $repository->setSetting('rules_title', 'Új szabályzat');

        $rows = DB::table('nova_settings')->where('key', 'rules_title')->get();
        $this->assertCount(1, $rows);
        $this->assertSame('Új szabályzat', $rows->first()->value);
        $this->assertSame('rules', $rows->first()->group);
    }

    public function test_the_repository_writes_a_new_setting_for_its_own_team(): void
    {
        $repository = new TeamSettingsRepository($this->hungary->id, 'rules');
        $repository->setSetting('rules_title', 'Szabályzat');

        $this->assertSame('Szabályzat', $this->settingValue($this->hungary, 'rules_title'));
        $this->assertNull($this->settingValue($this->romania, 'rules_title'));
        $this->assertNull((new TeamSettingsRepository($this->romania->id, 'rules'))->getSetting('rules_title'));
    }
}
