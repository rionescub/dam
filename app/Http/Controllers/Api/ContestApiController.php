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
        // Get the contests for the current team (scope automatically applies)
        $contests = Contest::all();

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
        // Find the contest by ID, scope ensures it's for the user's team
        $contest = Contest::findOrFail($id);

        return response()->json($contest);
    }

    public function getContest (Request $request) {
        $user = $request->user();
        $teamId = $user->current_team_id;
        $contest = Contest::where('team_id', $teamId)
            ->whereDate('end_date', '>', now())
            ->orderBy('start_date')
            ->firstOrFail();
        return response()->json($contest);
    }
}
