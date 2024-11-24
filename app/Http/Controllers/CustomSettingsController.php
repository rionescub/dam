<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TeamSettingsRepository;
use Illuminate\Support\Facades\Auth;

class CustomSettingsController extends \Outl1ne\NovaSettings\Http\Controllers\SettingsController
{
    public function getSettings(Request $request)
    {
        $user = Auth::user();
        $teamId = $user->current_team_id;

        if (!$teamId) {
            return response()->json(['error' => 'No team assigned'], 403);
        }

        $repo = new TeamSettingsRepository($teamId);
        $settings = $repo->allSettings()->pluck('value', 'key');

        return response()->json($settings);
    }

    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        $teamId = $user->current_team_id;

        if (!$teamId) {
            return response()->json(['error' => 'No team assigned'], 403);
        }

        $repo = new TeamSettingsRepository($teamId);

        foreach ($request->all() as $key => $value) {
            $repo->setSetting($key, $value);
        }

        return response()->json(['message' => 'Settings saved successfully.']);
    }
}
