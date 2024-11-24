<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class GalleryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {

        $teamId = Team::where('link', $request->link)->first()->id;

        $galleries = Gallery::where('team_id', $teamId)
            ->orderBy('year', 'desc')
            ->get()
            ->groupBy('year')
            ->map(function ($gallery, $year) {
                return [
                    'year' => $year,
                    'galleries' => $gallery->map(function ($item) {
                        return [
                            'title' => $item->title,
                            'year' => $item->year,
                            'contestant' => $item->contestant,
                            'image' => $item->image,
                        ];
                    })->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $galleries,
        ]);
    }
}
