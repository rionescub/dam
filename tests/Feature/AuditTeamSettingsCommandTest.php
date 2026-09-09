<?php

namespace Tests\Feature;

use App\Models\Team;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuditTeamSettingsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Team $romania;

    protected Team $hungary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->romania = Team::factory()->create(['name' => 'Romania', 'link' => 'ro']);
        $this->hungary = Team::factory()->create(['name' => 'Hungary', 'link' => 'hu']);
    }

    protected function seedSetting(?int $teamId, ?string $group, string $key, $value): void
    {
        DB::table('nova_settings')->insert([
            'team_id' => $teamId,
            'group' => $group,
            'key' => $key,
            'value' => $value,
        ]);
    }

    public function test_it_reports_the_damage_left_by_the_unscoped_writes(): void
    {
        // The same file on two teams: one team's upload landed on the other's row.
        $this->seedSetting($this->romania->id, 'rules', 'rules_pdf_file', 'shared-rulebook.pdf');
        $this->seedSetting($this->hungary->id, 'rules', 'rules_pdf_file', 'shared-rulebook.pdf');
        $this->seedSetting(null, null, 'terms_pdf_file', 'orphan-terms.pdf');
        $this->seedSetting($this->hungary->id, null, 'rules_title', 'Szabályzat');
        $this->seedSetting($this->hungary->id, 'rules', 'rules_removed_key', 'x');
        // The same key twice for one team: this is what blocks the unique index,
        // so it can only exist on a database that never got that index.
        Schema::table('nova_settings', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'key']);
        });
        $this->seedSetting($this->romania->id, null, 'contact_title', 'Contact');
        $this->seedSetting($this->romania->id, 'contact', 'contact_title', 'Contact us');

        $this->artisan('settings:audit')
            ->expectsOutputToContain('Orphan rows (team_id IS NULL): 1')
            ->expectsOutputToContain('Romania: contact_title x2 (groups: NULL, contact)')
            ->expectsOutputToContain('[FILE] rules_pdf_file = shared-rulebook.pdf')
            // Each expectation has to match a line of its own: Mockery lets only
            // one of them consume a given write.
            ->expectsOutputToContain('Romania (never), Hungary (never)')
            ->expectsOutputToContain('rules_title group=NULL -> rules')
            ->expectsOutputToContain('rules_removed_key')
            ->assertSuccessful();

        // A report on its own changes nothing.
        $this->assertSame(7, DB::table('nova_settings')->count());
    }

    public function test_it_repairs_groups_and_removes_orphans_on_request(): void
    {
        $this->seedSetting(null, null, 'terms_pdf_file', 'orphan-terms.pdf');
        $this->seedSetting($this->hungary->id, null, 'rules_title', 'Szabályzat');
        $this->seedSetting($this->romania->id, 'wrong', 'contact_title', 'Contact');

        $this->artisan('settings:audit --fix-groups --purge-orphans')->assertSuccessful();

        $this->assertSame(0, DB::table('nova_settings')->whereNull('team_id')->count());
        $this->assertSame('rules', DB::table('nova_settings')->where('key', 'rules_title')->value('group'));
        $this->assertSame('contact', DB::table('nova_settings')->where('key', 'contact_title')->value('group'));

        // Values are never touched: only the team that owns a row can fix it.
        $this->assertSame('Szabályzat', DB::table('nova_settings')->where('key', 'rules_title')->value('value'));
    }
}
