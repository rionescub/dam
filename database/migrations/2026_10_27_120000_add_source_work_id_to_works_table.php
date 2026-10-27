<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // Set on works cloned into the international contest, so the clone isn't repeated
            $table->foreignId('source_work_id')->nullable()->after('contest_id')->constrained('works')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_work_id');
        });
    }
};
