<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('contestant');
            $table->year('year');
            $table->foreignId('team_id')->constrained()->onDelete('cascade'); // Add team_id
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('galleries');
    }
};
