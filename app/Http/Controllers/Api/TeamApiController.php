<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\DB;

class TeamApiController extends Controller
{
    public function show($link)
    {
        $team = Team::where('link', $link)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        return response()->json($team);
    }

    public function getContent(Request $request, $link, $page)
    {
        $team = Team::where('link', $link)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $content = \DB::table('nova_settings')
            ->where('team_id', $team->id)
            ->where('group', $page)
            ->get()
            ->keyBy('key');

        return response()->json($content);
    }
}
