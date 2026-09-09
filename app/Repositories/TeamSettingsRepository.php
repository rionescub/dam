<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

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
        // A key is unique within a team (settings keys are page prefixed), so we
        // do not filter on `group`: rows whose group was lost would be invisible.
        return DB::table('nova_settings')
            ->where('team_id', $this->teamId)
            ->where('key', $key)
            ->value('value');
    }

    public function setSetting($key, $value)
    {
        DB::table('nova_settings')->updateOrInsert(
            [
                'team_id' => $this->teamId,
                'key' => $key,
            ],
            [
                'group' => $this->group,
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
