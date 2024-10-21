<?php

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

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
