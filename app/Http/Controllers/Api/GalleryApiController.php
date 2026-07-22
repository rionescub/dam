<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Work;
use App\Models\Gallery;
use App\Models\Contest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class GalleryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $team = Team::where('link', $request->link)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $galleryItems = Gallery::where('team_id', $team->id)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'year' => (int) $item->year,
                    'contestant' => $item->contestant,
                    'image' => $item->image,
                ];
            });

        // Include years that only have works flagged "view on front" but no
        // manually curated Gallery row yet, so a contest year never silently
        // disappears from the gallery just because nobody added a Gallery entry.
        $contestIds = Contest::where('team_id', $team->id)->pluck('id');

        $workItems = Work::with(['details', 'contest'])
            ->whereIn('contest_id', $contestIds)
            ->where('view_on_front', 1)
            ->get()
            ->map(function ($work) {
                return [
                    'title' => $work->name,
                    'year' => $work->contest?->start_date?->year,
                    'contestant' => $work->details?->full_name,
                    'image' => $work->file_path,
                ];
            })
            ->filter(fn ($item) => $item['year'] !== null);

        $galleries = $galleryItems->concat($workItems)
            ->groupBy('year')
            ->map(function ($items, $year) {
                return [
                    'year' => (int) $year,
                    'galleries' => $items->values()->toArray(),
                ];
            })
            ->sortByDesc('year')
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $galleries,
        ]);
    }
}
