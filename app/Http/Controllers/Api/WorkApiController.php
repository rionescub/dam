<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Team;
use App\Models\Work;
use App\Models\Contest;
use App\Models\WorkDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkApiController extends Controller
{
    /**
     * Get a list of works based on the user's role and team.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        // Define the per page count, defaulting to 8 if not provided.
        $perPage = $request->get('per_page', 100);

        if ($user->role === 'admin') {
            // Admins can view all works for their team
            $works = Work::with('details')
                ->paginate($perPage);
        } elseif ($user->role === 'judge') {
            // Judges can view works for contests they are judging within their team
            $contests = Contest::where('team_id', $user->current_team_id)
                ->where('end_date', '<=', Carbon::now())
                ->where('jury_date', '<=', Carbon::now())
                ->first();
            $works = Work::where('contest_id', $contests->id)
                ->with('details')
                ->with(['scores' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->paginate($perPage);
        } else {
            // Other users can only view their own works within their team
            $works = Work::where('user_id', $user->id)
                ->with('details')
                ->paginate($perPage);
        }
        return response()->json($works);
    }


    public function getFrontWorks(Request $request)
    {
        $team = Team::where('link', $request->link)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        // Get all contest IDs for the specified team
        $contestIds = Contest::where('team_id', $team->id)->pluck('id');

        // Fetch works that match any of the contest IDs and are set to show on front
        $works = Work::with('details')
            ->whereIn('contest_id', $contestIds)
            ->where('view_on_front', 1)
            ->get();

        return response()->json($works);
    }

    /**
     * Get artworks for the logged-in user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserArtworks(Request $request)
    {
        $user = $request->user();

        $artworks = Work::where('user_id', $user->id)
            ->with('details')
            ->get();

        return response()->json($artworks);
    }

    /**
     * Show a specific work based on ID and user's team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $work = Work::with('details')
            ->findOrFail($id);

        if ($user->role === 'judge') {
            $contests = Contest::where('team_id', $user->current_team_id)
                ->pluck('id');

            if (!in_array($work->contest_id, $contests->toArray())) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } elseif ($user->role !== 'admin' && $work->user_id !== $user->id) {
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

        // Validate request data
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description' => 'nullable|string',
            'contest_id' => 'required|exists:contests,id',
            'file' => $request->hasFile('file') ? 'file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable',
            'video_url' => 'nullable|url',
            'age_group' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'school' => 'nullable|string|max:255',
            'year' => 'required|string|max:20',
            'mentor' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $contest = Contest::findOrFail($request->contest_id);

        // Check if the current date is within the submission period
        $currentDate = Carbon::now();
        if ($currentDate->lt($contest->start_date) || $currentDate->gt($contest->end_date)) {
            return response()->json(['message' => 'Submissions are only allowed between the contest start and end dates.'], 403);
        }

        // Save work details in the Work model
        $work = new Work();
        $work->name = $request->title;
        $work->title_en = $request->title_en;
        $work->description = $request->description;
        $work->description_en = $request->description_en;
        $work->contest_id = $request->contest_id;
        $work->user_id = $user->id;

        // Handle file upload
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $path = 'public/works/' . $request->contest_id;
            $filePath = $request->file('file')->store($path);
            $work->file_path = str_replace('public/', '', $filePath);
        }

        // Save the video URL if provided
        if ($request->filled('video_url')) {
            $work->video_url = $request->video_url;
        }

        $work->save();

        // Save the work details in the WorkDetails model
        $workDetails = new WorkDetails();
        $workDetails->work_id = $work->id;
        $workDetails->full_name = $request->full_name;
        $workDetails->age_group = $request->age_group;
        $workDetails->city = $request->city;
        $workDetails->type = $request->type;
        $workDetails->country = $request->country;
        $workDetails->county = $request->county;
        $workDetails->school = $request->school;
        $workDetails->year = $request->year;
        $workDetails->mentor = $request->mentor;
        $workDetails->phone = $request->phone;

        $workDetails->save();

        return response()->json(['message' => 'Artwork submitted successfully', 'work' => $work], 201);
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
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'video_url' => [
                'nullable',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com|dropbox\.com|drive\.google\.com)\/.+$/'
            ],
        ]);

        $user = $request->user();

        $work = Work::where('user_id', $user->id)->findOrFail($id);

        // Ensure only the owner or judge can edit
        if ($work->user_id !== $user->id && $user->role !== 'judge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $work->title = $request->title;
        $work->description = $request->description;

        if ($request->hasFile('file')) {
            $path = 'public/works/' . $work->contest_id;
            $filePath = $request->file('file')->store($path);
            $work->file_path = str_replace('public/', '', $filePath);
        }

        // Update video URL if provided
        if ($request->filled('video_url')) {
            $work->video_url = $request->video_url;
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
        $teamId = $user->current_team_id;

        $work = Work::where('team_id', $teamId)->findOrFail($id);

        if ($work->user_id !== $user->id && $user->role !== 'judge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $work->delete(); // Soft delete

        return response()->json(['message' => 'Work deleted successfully']);
    }
}
