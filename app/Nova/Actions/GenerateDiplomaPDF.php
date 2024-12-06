<?php

namespace App\Nova\Actions;

use App\Models\Diploma;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateDiplomaPDF extends Action implements ShouldQueue
{
    use Queueable;

    /**
     * The name of the action.
     *
     * @return string
     */
    public $name = 'Generate Diploma PDF';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return \Laravel\Nova\Actions\Action|\Laravel\Nova\Actions\Action[]
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        // Array to hold download URLs
        $downloadLinks = [];

        foreach ($models as $diploma) {
            // Generate the PDF
            $pdf = PDF::loadView('pdf.diploma', compact('diploma'))
                ->setPaper('a4', 'landscape')
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('isFontSubsettingEnabled', true)
                ->setOption('isRemoteEnabled', true);

            // Define the file name and path
            $fileName = 'diploma_' . $diploma->id . '.pdf';
            $filePath = 'diplomas/' . $fileName;

            // Store the PDF in the storage (e.g., local disk)
            Storage::put($filePath, $pdf->output());

            // Generate a temporary signed URL valid for 5 minutes
            $url = URL::temporarySignedRoute(
                'download-diploma', // Route name
                now()->addMinutes(5), // Expiration time
                ['diploma' => $diploma->id] // Route parameters
            );

            // Add to the download links array
            $downloadLinks[] = "<a href='{$url}' target='_blank'>Download {$fileName}</a>";
        }

        // If only one diploma, return a single download link
        if ($downloadLinks->count() === 1) {
            return Action::download($downloadLinks[0], 'diploma.pdf');
        }

        // If multiple diplomas, return a message with all download links
        $message = implode('<br>', $downloadLinks);
        return Action::message($message);
    }

    /**
     * The fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
