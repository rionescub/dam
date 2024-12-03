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
        //$locale = $request->server('HTTPS_LOCALE') ?: 'ro';

        $team = Team::where('link', $request->link)->first();
        $sponsors = Sponsor::where('team_id', $team->id)->select('id', 'name', 'image', 'url', 'type')->get();

        return response()->json($sponsors);
    }
}
