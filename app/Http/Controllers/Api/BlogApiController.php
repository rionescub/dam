<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BlogApiController extends Controller
{
    /**
     * Display a listing of blogs based on the team's locale.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the locale from the request
        $locale = $request->get('locale', app()->getLocale());

        // Find the team associated with the locale
        $team = Team::where('link', $locale)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        // Define the per page count, defaulting to 10 if not provided
        $perPage = $request->get('per_page', 10);

        // Fetch blogs associated with the team
        $blogs = Blog::with('user')
            ->whereHas('user', function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })
            ->paginate($perPage);

        return response()->json($blogs);
    }

    /**
     * Display the specified blog post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Get the locale from the request
        $locale = $request->get('locale', app()->getLocale());

        // Find the team associated with the locale
        $team = Team::where('link', $locale)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        // Find the blog post within the team
        $blog = Blog::with('user')
            ->findOrFail($id);

        if ($blog->user->team_id !== $team->id) {
            return response()->json(['error' => 'Blog post not found'], 404);
        }

        return response()->json($blog);
    }

    /**
     * Store a newly created blog post.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Create the blog post
        $blog = new Blog();
        $blog->user_id = $user->id;
        $blog->team_id = $user->current_team_id; // Ensure user has current_team_id
        $blog->title = $request->title;
        $blog->content = $request->content;
        $blog->save();

        return response()->json(['message' => 'Blog post created successfully', 'blog' => $blog], 201);
    }

    /**
     * Update the specified blog post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
}
