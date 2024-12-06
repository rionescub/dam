<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DiplomaController extends Controller
{
    /**
     * Handle the download of a diploma PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Diploma  $diploma
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    public function download(Request $request, Diploma $diploma)
    {
        // Eager load necessary relationships
        $diploma->load('user', 'work', 'contest', 'work.details');

        // Generate PDF from the diploma data
        $pdf = Pdf::loadView('pdf.diploma', compact('diploma'))
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('encoding', 'UTF-8');

        // Create a sanitized file name
        $fileName = 'diplomas/diploma_' . $diploma->id . "_" .
            str_replace(' ', '_', $diploma->work->contest->name) . '_' .
            str_replace(' ', '_', $diploma->work->details->full_name) . '.pdf';

        // Store PDF in the public disk
        Storage::disk('public')->put($fileName, $pdf->output());

        // Download the file using the local path for API response
        $localFilePath = storage_path("app/public/{$fileName}");

        // Return download response with headers for API
        return response()->download(
            $localFilePath,
            "diploma_{$diploma->id}_" .
                str_replace(' ', '_', $diploma->work->contest->name) . "_" .
                str_replace(' ', '_', $diploma->work->details->full_name) . ".pdf"
        )
            ->deleteFileAfterSend(true);
    }
}
