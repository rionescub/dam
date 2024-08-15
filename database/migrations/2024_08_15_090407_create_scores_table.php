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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('creativity_score')->default(0); // Score from 0 to 10
            $table->unsignedTinyInteger('link_score')->default(0);       // Score from 0 to 10
            $table->unsignedTinyInteger('aesthetic_score')->default(0);  // Score from 0 to 10
            $table->unsignedTinyInteger('total_score')->default(0);      // Score from 0 to 30
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
