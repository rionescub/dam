<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contest;

class ContestApiController extends Controller
{
    /**
     * Get all contests belonging to the team of the logged-in user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the authenticated user and their team
        $user = $request->user();
        $teamId = $user->current_team_id;

        // Fetch contests that belong to the user's team
        $contests = Contest::where('team_id', $teamId)->get();

        return response()->json($contests);
    }

    /**
     * Get a specific contest belonging to the user's team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Get the authenticated user and their team
        $user = $request->user();
        $teamId = $user->current_team_id;

        // Fetch the contest by ID that belongs to the user's team
        $contest = Contest::where('team_id', $teamId)->findOrFail($id);

        return response()->json($contest);
    }
}
