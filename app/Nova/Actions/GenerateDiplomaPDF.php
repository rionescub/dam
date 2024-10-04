<?php

namespace App\Nova\Actions;

use App\Models\Diploma;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class GenerateDiplomaPDF extends Action
{
    use Queueable;

    public $confirmable = false;

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
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $diploma) {
            // Load the view and pass the diploma data
            $pdf = PDF::loadView('pdf.diploma', compact('diploma'))
                ->setPaper('a4', 'landscape');

            // You can store the PDF if needed, for example in public storage
            $fileName = 'diplomas/diploma_' . $diploma->id . '.pdf';
            Storage::put($fileName, $pdf->output());

            // Return a download link
            return Action::download(Storage::url($fileName), 'diploma_' . $diploma->id . '.pdf');
        }
    }

    /**
     * The fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $this->request = $request;
        return [];
    }
}
