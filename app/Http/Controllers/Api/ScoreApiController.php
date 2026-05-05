<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Work;
use App\Models\Score;
use App\Models\Contest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            $contest = Contest::where('team_id', $teamId)
                ->where('jury_date', '<=', Carbon::now())
                ->where('ceremony_date', '>=', Carbon::now())
                ->first();

            $works = $contest
                ? Work::where('contest_id', $contest->id)->pluck('id')
                : collect([]);

            $scores = Score::whereIn('work_id', $works)
                ->where('user_id', $user->id)
                ->get();
        } else {
            $works = Work::where('user_id', $user->id)
                ->whereHas('contest', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                })
                ->pluck('id');

            $scores = Score::whereIn('work_id', $works)->get();
        }

        return response()->json($scores);
    }

    /**
     * Get an individual score by ID.
     *
     * @param  int  $id  Work ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

        $work = Work::whereHas('contest', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->findOrFail($id);

        $scores = Score::where('work_id', $work->id)
            ->where('user_id', $user->id)
            ->get();

        if ($user->role !== 'admin' && $scores->pluck('user_id')->doesntContain($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($scores);
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

        if (! in_array($user->role, ['judge', 'admin'])) {
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

        $attribute = $request->input('attribute');
        $val = $request->input('score');

        $work = Work::with('contest')->findOrFail($request->work_id);
        if ((int) $work->contest->team_id !== (int) $user->current_team_id) {
            return response()->json(['message' => 'User not allowed to rate works in contest'], 403);
        }

        $now = Carbon::now();
        if ($now->lt(Carbon::parse($work->contest->jury_date)) || $now->gt(Carbon::parse($work->contest->ceremony_date))) {
            return response()->json(['message' => 'Judging is only allowed from the jury date (inclusive) until the ceremony date (inclusive).'], 403);
        }

        $score = Score::firstOrNew(['work_id' => $work->id, 'user_id' => $user->id]);

        if ($score->exists && $score->is_finalized) {
            return response()->json(['message' => 'Score has already been finalized and cannot be changed.'], 403);
        }

        $allowed = ['creativity_score', 'link_score', 'aesthetic_score'];
        if ($attribute && in_array($attribute, $allowed)) {
            if (!$score->exists) {
                $score->creativity_score = 1;
                $score->link_score = 1;
                $score->aesthetic_score = 1;
            }
            $score->$attribute = $val;
        }

        $score->save();

        return response()->json(['message' => 'Score created/updated successfully', 'score' => $score], 201);
    }

    /**
     * Update an existing score by score ID (must belong to same user/work/contest window).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Score ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'work_id' => 'required|exists:works,id',
            'score' => 'required|numeric|min:0|max:10',
            'attribute' => 'required|string|in:creativity_score,link_score,aesthetic_score',
        ]);

        $work = Work::with('contest')->findOrFail($request->work_id);
        if ((int) $work->contest->team_id !== (int) $user->current_team_id) {
            return response()->json(['message' => 'User not allowed to rate works in contest'], 403);
        }

        $score = Score::where('id', $id)
            ->where('user_id', $user->id)
            ->where('work_id', $work->id)
            ->firstOrFail();

        if (! in_array($user->role, ['judge', 'admin'])) {
            return response()->json(['message' => 'Role not permitted to rate'], 403);
        }

        if ($score->is_finalized) {
            return response()->json(['message' => 'Score has already been finalized and cannot be changed.'], 403);
        }

        $now = Carbon::now();
        if ($now->lt(Carbon::parse($work->contest->jury_date)) || $now->gt(Carbon::parse($work->contest->ceremony_date))) {
            return response()->json(['message' => 'Judging is only allowed from the jury date (inclusive) until the ceremony date (inclusive).'], 403);
        }

        $attribute = $request->attribute;
        $score->$attribute = $request->score;
        $score->save();

        return response()->json(['message' => 'Score updated successfully', 'score' => $score]);
    }

    /**
     * Finalize a score for a work by the current user (work ID).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Work ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalize(Request $request, $id)
    {
        $user = $request->user();

        $work = Work::with('contest')->whereHas('contest', function ($q) use ($user) {
            $q->where('team_id', $user->current_team_id);
        })->findOrFail($id);

        if (! in_array($user->role, ['admin', 'judge'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $now = Carbon::now();
        if ($now->lt(Carbon::parse($work->contest->jury_date)) || $now->gt(Carbon::parse($work->contest->ceremony_date))) {
            return response()->json(['message' => 'Finalization is only allowed between the jury date and ceremony date.'], 403);
        }

        $score = Score::firstOrNew([
            'work_id' => $work->id,
            'user_id' => $user->id,
        ]);

        if ($score->is_finalized) {
            return response()->json(['message' => 'Score has already been finalized.'], 200);
        }

        $score->is_finalized = true;
        $score->save();

        return response()->json(['message' => 'Score finalized successfully', 'score' => $score]);
    }
}
