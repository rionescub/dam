<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Contest;
use App\Models\Testimonials;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestimonialsApiController extends Controller
{
    /**
     * Get a list of sponsors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch only the relevant fields: name, logo, url
        //$locale = $request->server('HTTPS_LOCALE') ?: 'ro';
        $team = Team::where('link', $request->link)->first();
        $testimonials = Testimonials::where('team_id', $team->id)->get();

        return response()->json($testimonials);
    }
}
