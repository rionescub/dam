<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Outl1ne\NovaSettings\NovaSettings;

return new class extends Migration
{
    /**
     * Settings are team scoped, but nova_settings is created before the `teams`
     * table exists, and on deployments where the table was created by hand the
     * constraints may be missing altogether. Add them once teams is there.
     */
    public function up(): void
    {
        $table = NovaSettings::getSettingsTableName();

        if (! Schema::hasTable($table) || ! Schema::hasTable('teams')) {
            return;
        }

        // One row per key and team: reads pick a row by (team_id, key).
        if (! Schema::hasIndex($table, "{$table}_team_id_key_unique")) {
            if ($this->duplicates($table)->isEmpty()) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unique(['team_id', 'key']);
                });
            } else {
                echo "  Skipped the unique (team_id, key) index on {$table}: duplicate rows exist. Run `php artisan settings:audit`.\n";
            }
        }

        // SQLite cannot add a constraint to an existing table.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $hasForeignKey = collect(Schema::getForeignKeys($table))
            ->contains(fn ($foreignKey) => in_array('team_id', $foreignKey['columns']));

        if (! $hasForeignKey) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $table = NovaSettings::getSettingsTableName();

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['team_id']);
            });
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->dropUnique(['team_id', 'key']);
        });
    }

    /**
     * Rows that would violate the unique index.
     */
    protected function duplicates(string $table)
    {
        return DB::table($table)
            ->select('team_id', 'key')
            ->groupBy('team_id', 'key')
            ->havingRaw('count(*) > 1')
            ->get();
    }
};
