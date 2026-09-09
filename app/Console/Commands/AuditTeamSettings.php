<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\File;

/**
 * Reports (and optionally repairs) the damage caused by nova-settings writing
 * setting rows without a team_id: values leaked into other teams' rows and
 * orphan rows with no team at all.
 */
class AuditTeamSettings extends Command
{
    protected $signature = 'settings:audit
                            {--fix-groups : Restore the `group` column from the field definitions}
                            {--purge-orphans : Delete rows that have no team_id}';

    protected $description = 'Audit nova_settings for cross-team leaks, orphan rows and missing groups';

    public function handle()
    {
        [$groupOf, $isFile] = $this->fieldMap();

        $teams = Team::pluck('name', 'id');
        $rows = DB::table('nova_settings')->get();

        $this->info("Teams: {$teams->count()} | setting rows: {$rows->count()}");

        // 1. Orphan rows: written by the package with no team context.
        $orphans = $rows->whereNull('team_id');
        $this->newLine();
        $this->line('<comment>Orphan rows (team_id IS NULL)</comment>: '.$orphans->count());
        foreach ($orphans as $row) {
            $this->line("  - {$row->key} = ".$this->short($row->value));
        }

        // 2. The same key stored twice for one team: what blocks the unique index.
        $duplicates = $rows
            ->whereNotNull('team_id')
            ->groupBy(fn ($row) => $row->team_id.'|'.$row->key)
            ->filter(fn ($group) => $group->count() > 1);

        $this->newLine();
        $this->line('<comment>Duplicate rows (same team and key)</comment>: '.$duplicates->count());
        foreach ($duplicates as $group) {
            $first = $group->first();
            $team = $teams[$first->team_id] ?? "team {$first->team_id}";
            $this->line("  - {$team}: {$first->key} x{$group->count()} (groups: ".$group->pluck('group')->map(fn ($g) => $g ?? 'NULL')->implode(', ').')');
        }

        // 3. Values shared by more than one team: one team's save overwrote another's.
        $this->newLine();
        $this->line('<comment>Values shared across teams (leaked)</comment>');
        $shared = $rows
            ->filter(fn ($row) => filled($row->value) && $row->team_id !== null)
            ->groupBy(fn ($row) => $row->key.'|'.$row->value)
            ->filter(fn ($group) => $group->pluck('team_id')->unique()->count() > 1);

        if ($shared->isEmpty()) {
            $this->line('  none');
        }

        foreach ($shared as $group) {
            $first = $group->first();
            $kind = ($isFile[$first->key] ?? false) ? 'FILE' : 'text';
            $owners = $group
                ->sortBy('updated_at')
                ->map(fn ($row) => ($teams[$row->team_id] ?? "team {$row->team_id}").' ('.($row->updated_at ?? 'never').')')
                ->implode(', ');

            $this->line("  [{$kind}] {$first->key} = ".$this->short($first->value));
            $this->line("        {$owners}");
        }

        // 4. Rows whose group does not match the settings page they belong to.
        $wrongGroup = $rows->filter(function ($row) use ($groupOf) {
            $expected = $groupOf[$row->key] ?? null;

            return $expected !== null && $row->group !== $expected;
        });

        $this->newLine();
        $this->line('<comment>Rows with a wrong or missing group</comment> (invisible on the site): '.$wrongGroup->count());
        foreach ($wrongGroup as $row) {
            $team = $teams[$row->team_id] ?? "team {$row->team_id}";
            $this->line("  - {$team}: {$row->key} group=".($row->group ?? 'NULL')." -> {$groupOf[$row->key]}");
        }

        // 5. Keys that no longer exist in the field definitions.
        $unknown = $rows->filter(fn ($row) => ! isset($groupOf[$row->key]))->pluck('key')->unique();
        $this->newLine();
        $this->line('<comment>Unknown keys</comment> (no field defines them): '.$unknown->count());
        foreach ($unknown as $key) {
            $this->line("  - {$key}");
        }

        // 6. Per team, which keys are missing entirely.
        $this->newLine();
        $this->line('<comment>Missing settings per team</comment>');
        foreach ($teams as $teamId => $name) {
            $have = $rows->where('team_id', $teamId)->filter(fn ($row) => filled($row->value))->pluck('key');
            $missing = collect(array_keys($groupOf))->diff($have);
            $this->line("  - {$name}: ".$missing->count().' of '.count($groupOf).' empty');
        }

        if ($this->option('fix-groups')) {
            $fixed = 0;
            foreach ($wrongGroup->whereNotNull('team_id') as $row) {
                $fixed += DB::table('nova_settings')
                    ->where('team_id', $row->team_id)
                    ->where('key', $row->key)
                    ->update(['group' => $groupOf[$row->key]]);
            }
            $this->newLine();
            $this->info("Repaired group on {$fixed} row(s).");
        }

        if ($this->option('purge-orphans')) {
            $deleted = DB::table('nova_settings')->whereNull('team_id')->delete();
            $this->info("Deleted {$deleted} orphan row(s).");
        }

        return self::SUCCESS;
    }

    /**
     * Build key => group and key => isFileField maps from app/Nova/Settings.
     *
     * @return array{0: array<string, string>, 1: array<string, bool>}
     */
    protected function fieldMap(): array
    {
        $groupOf = [];
        $isFile = [];

        foreach (glob(app_path('Nova/Settings/*.php')) as $path) {
            $group = strtolower(pathinfo($path, PATHINFO_FILENAME));

            foreach (require $path as $field) {
                $groupOf[$field->attribute] = $group;
                $isFile[$field->attribute] = $field instanceof File;
            }
        }

        return [$groupOf, $isFile];
    }

    protected function short($value): string
    {
        $value = (string) $value;

        return strlen($value) > 60 ? substr($value, 0, 57).'...' : $value;
    }
}
