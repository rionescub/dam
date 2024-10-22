<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contest_sponsor', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['team_id']);
            // Drop the column itself
            $table->dropColumn('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contest_sponsor', function (Blueprint $table) {
            // Add the team_id column back
            $table->foreignId('team_id')->constrained();
        });
    }
};
