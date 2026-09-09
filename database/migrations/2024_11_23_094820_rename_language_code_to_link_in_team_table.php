<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLanguageCodeToLinkInTeamTable extends Migration
{
    public function up()
    {
        // The create migration was later changed to define `link` directly, so
        // on a fresh database there is nothing left to rename.
        if (Schema::hasColumn('teams', 'language_code')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->renameColumn('language_code', 'link');
            });
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->unique('link');
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['link']);
        });

        if (Schema::hasColumn('teams', 'link')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->renameColumn('link', 'language_code');
            });
        }
    }
}
