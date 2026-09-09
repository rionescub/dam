<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Outl1ne\NovaSettings\Models\Settings as NovaSettingsModel;

/**
 * Team aware replacement for the nova-settings package model.
 *
 * The package queries settings by `key` alone (see SettingsController::save()
 * and ::deleteImage()), which in our multi-tenant `nova_settings` table hits
 * whichever team happens to own the first matching row. That is how uploads
 * made by one team ended up overwriting another team's files and images.
 */
class TeamSetting extends NovaSettingsModel
{
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope('team', function (Builder $query) {
            $teamId = static::currentTeamId();

            // Without a team context we return nothing rather than every team's rows.
            $teamId
                ? $query->where('team_id', $teamId)
                : $query->whereRaw('1 = 0');
        });

        static::creating(function (self $setting) {
            $setting->team_id = $setting->team_id ?? static::currentTeamId();
            $setting->group = $setting->group ?? static::currentGroup();
        });
    }

    public static function currentTeamId()
    {
        return Auth::user()?->current_team_id;
    }

    /**
     * The settings page currently being saved ("rules", "terms", ...), used as
     * the `group` of rows the package creates on its own.
     */
    protected static function currentGroup()
    {
        $request = request();

        $path = $request->route('pathName')
            ?? $request->route('path')
            ?? $request->get('path');

        return $path ? Str::lower(Str::slug($path)) : null;
    }

    /**
     * The primary key is `key`, which is only unique *per team*. Without the
     * extra constraint an update would touch every team's row at once.
     */
    protected function setKeysForSaveQuery($query)
    {
        return parent::setKeysForSaveQuery($query)
            ->where('team_id', $this->team_id ?? static::currentTeamId());
    }

    protected function setKeysForSelectQuery($query)
    {
        return parent::setKeysForSelectQuery($query)
            ->where('team_id', $this->team_id ?? static::currentTeamId());
    }
}
