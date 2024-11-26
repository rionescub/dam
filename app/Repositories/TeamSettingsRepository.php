<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Outl1ne\NovaSettings\SettingsHelper;

class TeamSettingsRepository
{
    protected $teamId;
    protected $group;

    public function __construct($teamId, $group = 'default')
    {
        $this->teamId = $teamId;
        $this->group = $group;
    }

    public function getSetting($key)
    {
        return DB::table('nova_settings')
            ->where('team_id', $this->teamId)
            ->where('group', $this->group)
            ->where('key', $key)
            ->value('value');
    }

    public function setSetting($key, $value)
    {
        DB::table('nova_settings')->updateOrInsert(
            [
                'team_id' => $this->teamId,
                'group' => $this->group,
                'key' => $key,
            ],
            [
                'value' => $value,
                'updated_at' => now(),
            ]
        );
    }

    public function allSettings()
    {
        return DB::table('nova_settings')
            ->where('team_id', $this->teamId)
            ->where('group', $this->group)
            ->get();
    }
}
