<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Outl1ne\NovaSettings\NovaSettings;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The `teams` table this column points at is only created by a later
     * migration, so the foreign key is added afterwards by
     * 2024_08_14_150000_add_team_constraints_to_nova_settings_table.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable(NovaSettings::getSettingsTableName())) {
            return;
        }

        // Settings table
        Schema::create(NovaSettings::getSettingsTableName(), function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('group')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(NovaSettings::getSettingsTableName());
    }
};
