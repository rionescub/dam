<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Contest;

class WorkApiController extends Controller
{
    /**
     * Get a list of works based on the user's role.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'judge') {
            // Fetch works related to contests the judge is assigned to
            $contests = Contest::where('judge_id', $user->id)->pluck('id');
            $works = Work::whereIn('contest_id', $contests)->get();
        } else {
            // Fetch only works belonging to the user
            $works = Work::where('user_id', $user->id)->get();
        }

        return response()->json($works);
    }

    /**
     * Get an individual work by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $work = Work::findOrFail($id);

        // Check if the user is allowed to view the work
        if ($user->role === 'judge') {
            $contests = Contest::where('judge_id', $user->id)->pluck('id');
            if (!in_array($work->contest_id, $contests->toArray())) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } elseif ($work->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($work);
    }

    /**
     * Create a new work.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contest_id' => 'required|exists:contests,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Example of file upload validation
        ]);

        $user = $request->user();

        $work = new Work();
        $work->title = $request->title;
        $work->description = $request->description;
        $work->contest_id = $request->contest_id;
        $work->user_id = $user->id;

        // Handle file upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('works');
            $work->file_path = $path;
        }

        $work->save();

        return response()->json(['message' => 'Work created successfully', 'work' => $work], 201);
    }

    /**
     * Update an existing work.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = $request->user();
        $work = Work::findOrFail($id);

        // Ensure only the owner or judge can edit
        if ($work->user_id !== $user->id && $user->role !== 'judge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $work->title = $request->title;
        $work->description = $request->description;

        // Handle file upload if a new file is uploaded
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('works');
            $work->file_path = $path;
        }

        $work->save();

        return response()->json(['message' => 'Work updated successfully', 'work' => $work]);
    }

    /**
     * Delete a work.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $work = Work::findOrFail($id);

        // Ensure only the owner or judge can delete
        if ($work->user_id !== $user->id && $user->role !== 'judge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $work->delete();

        return response()->json(['message' => 'Work deleted successfully']);
    }
}
