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
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type');
            $table->foreignId('parent_contest_id')->nullable()->constrained('contests')->onDelete('cascade');
            $table->text('rules');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Belongs to a user
            $table->timestamps();
        });

        Schema::create('contest_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contest_user');
        Schema::dropIfExists('contests');
    }
};
