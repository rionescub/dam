<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diploma;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DiplomaApiController extends Controller
{
    /**
     * View all diplomas of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $diplomas = Diploma::where('user_id', $user->id)->get();

        return response()->json($diplomas);
    }

    /**
     * Download a specific diploma as a PDF.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request, $id)
    {
        $user = $request->user();
        $diploma = Diploma::where('user_id', $user->id)->findOrFail($id);

        // Generate PDF from the diploma data
        $pdf = Pdf::loadView('pdf.diploma', compact('diploma'))
            ->setPaper('a4', 'landscape');

        // Store PDF temporarily if needed
        $fileName = 'diplomas/diploma_' . $diploma->id . '.pdf';
        Storage::put($fileName, $pdf->output());

        // Optionally, you could return the file from storage if you want to save it permanently
        return response()->download(storage_path("app/{$fileName}"), "diploma_{$diploma->id}.pdf")
            ->deleteFileAfterSend(true);
    }
}
