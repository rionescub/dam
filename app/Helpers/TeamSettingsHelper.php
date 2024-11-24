<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TeamSettingsRepository;

class TeamSettingsHelper
{
    public static function getTeamSetting($key, $teamId)
    {
        return DB::table('nova_settings')
            ->where('team_id', $teamId)
            ->where('key', $key)
            ->value('value');
    }

    public static function setTeamSetting($key, $value, $teamId)
    {
        DB::table('nova_settings')->updateOrInsert(
            ['team_id' => $teamId, 'key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    public static function makeSetting($key, $name, $class)
    {
        return $class::make($name, $key);
    }
}
