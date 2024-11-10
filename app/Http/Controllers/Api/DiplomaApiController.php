<?php

namespace App\Http\Controllers\Api;

use App\Models\Diploma;
use App\Models\WorkDetails;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DiplomaApiController extends Controller
{
    /**
     * View all diplomas of the authenticated user within their team.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $diplomas = Diploma::where('user_id', $user->id)->with('user', 'work', 'work.contest', 'work.details')
            ->get();
        return response()->json($diplomas);
    }

    /**
     * Download a specific diploma as a PDF, ensuring it belongs to the user's team.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request, $id)
    {
        // Fetch the diploma and ensure it belongs to the user's team
        $diploma = Diploma::where('id', $id)
            ->with('user', 'work', 'contest', 'work.details')
            ->findOrFail($id);

        if ($diploma->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized to download diploma'], Response::HTTP_FORBIDDEN);
        }

        // Generate PDF from the diploma data
        $pdf = Pdf::loadView('pdf.diploma', compact('diploma'))
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('isRemoteEnabled', true);

        // Store PDF in the public disk
        $fileName = 'diplomas/diploma_' . $diploma->id . "_" . str_replace(' ', '_', $diploma->work->contest->name) . '_' . str_replace(' ', '_', $diploma->work->details->full_name) . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        // Download the file using the local path for API response
        $localFilePath = storage_path("app/public/{$fileName}");

        // Return download response with headers for API
        return response()->download($localFilePath, "diploma_{$diploma->id}_" . str_replace(' ', '_', $diploma->work->contest->name) . "_" . str_replace(' ', '_', $diploma->work->details->full_name) . ".pdf")
            ->deleteFileAfterSend(true);
    }
}
