<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sponsor;

class SponsorsApiController extends Controller
{
    /**
     * Get a list of sponsors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Fetch only the relevant fields: name, logo, url
        $sponsors = Sponsor::select('id', 'name', 'image', 'url')->get();

        return response()->json($sponsors);
    }
}
