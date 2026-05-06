<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_details', function (Blueprint $table) {
            $table->string('school_address')->nullable()->after('school');
        });
    }

    public function down(): void
    {
        Schema::table('work_details', function (Blueprint $table) {
            $table->dropColumn('school_address');
        });
    }
};