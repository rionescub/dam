<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\Work;
use Illuminate\Support\Facades\Auth;

class ScoreApiController extends Controller
{
    /**
     * Get a list of scores based on the user's role.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'judge') {
            // Fetch scores related to the contests the judge is assigned to
            $contests = $user->contests()->pluck('id');
            $works = Work::whereIn('contest_id', $contests)->pluck('id');
            $scores = Score::whereIn('work_id', $works)->get();
        } else {
            // Fetch only scores for the user's own works
            $works = Work::where('user_id', $user->id)->pluck('id');
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
        $score = Score::findOrFail($id);
        $work = $score->work;

        // Ensure the user can view the score
        if ($user->role === 'judge') {
            $contests = $user->contests()->pluck('id');
            if (!in_array($work->contest_id, $contests->toArray())) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } elseif ($work->user_id !== $user->id) {
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

        if ($user->role !== 'judge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'work_id' => 'required|exists:works,id',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $work = Work::findOrFail($request->work_id);

        // Check if the work belongs to a contest the judge is assigned to
        if (!in_array($work->contest_id, $user->contests()->pluck('id')->toArray())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Create the score
        $score = new Score();
        $score->work_id = $work->id;
        $score->user_id = $user->id; // Judge ID
        $score->score = $request->score;
        $score->if_submitted = false; // Default to false, until explicitly submitted
        $score->save();

        return response()->json(['message' => 'Score created successfully', 'score' => $score], 201);
    }

    /**
     * Update an existing score (judges only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $score = Score::findOrFail($id);

        if ($user->role !== 'judge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if the score's work belongs to a contest the judge is assigned to
        if (!in_array($score->work->contest_id, $user->contests()->pluck('id')->toArray())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prevent updating if the score is already submitted
        if ($score->if_submitted) {
            return response()->json(['message' => 'Score already submitted, cannot be edited'], 403);
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'if_submitted' => 'boolean',
        ]);

        $score->score = $request->score;
        $score->if_submitted = $request->if_submitted ?? $score->if_submitted;
        $score->save();

        return response()->json(['message' => 'Score updated successfully', 'score' => $score]);
    }
}
