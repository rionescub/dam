<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLanguageCodeToLinkInTeamTable extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('language_code', 'link');
            $table->unique('link');
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['link']);
            $table->renameColumn('link', 'language_code');
        });
    }
}
