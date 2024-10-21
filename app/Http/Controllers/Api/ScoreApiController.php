<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Work;
use App\Models\Score;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScoreApiController extends Controller
{
    /**
     * Get a list of scores based on the user's role and team.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

        if ($user->role === 'judge') {
            // Fetch scores related to contests assigned to the judge within their team
            $contests = $user->contests()->where('team_id', $teamId)->pluck('id');
            $works = Work::whereIn('contest_id', $contests)->where('team_id', $teamId)->pluck('id');
            $scores = Score::whereIn('work_id', $works)->get();
        } else {
            // Fetch only scores for the user's own works within their team
            $works = Work::where('user_id', $user->id)->where('team_id', $teamId)->pluck('id');
            $scores = Score::whereIn('work_id', $works)->get();
        }

        return response()->json($scores);
    }

    /**
     * Get an individual score by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

        $score = Score::where('id', $id)
            ->whereHas('work', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->firstOrFail();

        // Additional validation for judges or users accessing their scores
        if ($user->role !== 'admin' && $score->work->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($score);
    }

    /**
     * Create a new score (judges only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['judge', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'work_id' => 'required|exists:works,id',
        ]);

        $request->validate([
            'creativity_score' => 'nullable|numeric|min:0|max:10',
            'link_score' => 'nullable|numeric|min:0|max:10',
            'aesthetic_score' => 'nullable|numeric|min:0|max:10',
        ]);

        $data = [
            'creativity_score' => $request->input('creativity_score', 0),
            'link_score' => $request->input('link_score', 0),
            'aesthetic_score' => $request->input('aesthetic_score', 0),
        ];

        $work = Work::findOrFail($request->work_id);
        $contest = $work->contest;

        // Check if the current date is within the judging period
        $currentDate = Carbon::now();
        if ($currentDate->lt($contest->end_date) || $currentDate->gt($contest->jury_date)) {
            return response()->json(['message' => 'Judging is only allowed between the contest end date and jury date.'], 403);
        }

        // Create the score or update if it exists
        $score = Score::firstOrNew(['work_id' => $work->id, 'user_id' => $user->id]);
        $score->fill($data);
        $score->save();

        return response()->json(['message' => 'Score created/updated successfully', 'score' => $score], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $score = Score::where('id', $id)
            ->firstOrFail();

        if (!in_array($user->role, ['judge', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:10',
            'attribute' => 'required|string|in:creativity_score,link_score,aesthetic_score',
        ]);

        $work = $score->work;
        $contest = $work->contest;

        // Check if the current date is within the judging period
        $currentDate = Carbon::now();
        if ($currentDate->lt($contest->end_date) || $currentDate->gt($contest->jury_date)) {
            return response()->json(['message' => 'Judging is only allowed between the contest end date and jury date.'], 403);
        }

        // Update the specific attribute of the score
        $attribute = $request->attribute;
        $score->$attribute = $request->score;
        $score->save();

        return response()->json(['message' => 'Score updated successfully', 'score' => $score]);
    }

    /**
     * Finalize a score.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalize(Request $request, $id)
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

        // Find the score and ensure it belongs to the user's team
        $score = Score::where('id', $id)
            ->whereHas('work', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->firstOrFail();

        // Check if the user has the proper role to finalize a score
        if (!in_array($user->role, ['admin', 'judge'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure the current date is within the judging period (between end_date and jury_date)
        $currentDate = now();
        $contest = $score->work->contest;

        if ($currentDate->lt($contest->end_date) || $currentDate->gt($contest->jury_date)) {
            return response()->json(['message' => 'Finalization is only allowed between the contest end date and jury date.'], 403);
        }

        // Finalize the score
        $score->is_finalized = true;
        $score->save();

        return response()->json(['message' => 'Score finalized successfully', 'score' => $score]);
    }
}
