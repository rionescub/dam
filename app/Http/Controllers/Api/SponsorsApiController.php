<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Contest;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SponsorsApiController extends Controller
{
    /**
     * Get a list of sponsors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch only the relevant fields: name, logo, url
        $locale = $request->server('HTTP_LOCALE') ?: 'en';
        $team = Team::where('language_code', $locale)->first();
        $contest = Contest::where('team_id', $team->id)->firstOrFail();
        $sponsors = Contest::find($contest->id)->sponsors()->select('sponsors.id', 'name', 'image', 'url')->get();

        return response()->json($sponsors);
    }
}
