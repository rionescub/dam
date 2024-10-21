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
        Schema::create('work_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained()->onDelete('cascade'); // Link to the work
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade'); // Nullable in case a work isn't linked to a team
            $table->string('full_name'); // Contestant Name
            $table->string('country')->nullable(); // Country of the contestant
            $table->string('county')->nullable(); // County
            $table->string('city'); // City
            $table->string('phone')->nullable(); // Mentor Phone
            $table->string('mentor')->nullable(); // Mentor Name
            $table->string('school')->nullable(); // School Name
            $table->string('school_director')->nullable(); // School Director (optional, if needed)
            $table->string('year')->nullable(); // Year (or Class)
            $table->string('age_group'); // Age group of the contestant (e.g., 6-11, 14-18)
            $table->string('type'); // Type of artwork (e.g., video, image, artwork)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_details');
    }
};
