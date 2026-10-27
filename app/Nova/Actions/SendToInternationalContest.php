<?php

namespace App\Nova\Actions;

use App\Nova\Filters\SentFromContestFilter;
use App\Services\InternationalContestSender;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Same as app:send-to-international-contest --contest=ID, for one contest from Nova.
 */
class SendToInternationalContest extends Action
{
    public $name = 'Send winners to international';

    public $confirmText = 'Copy every first-place work of this contest into the open international contest?';

    public $confirmButtonText = 'Send';

    public function handle(ActionFields $fields, Collection $models)
    {
        $sender = app(InternationalContestSender::class);
        $target = $sender->targetContest();

        if (! $target) {
            return Action::danger('No open international contest found.');
        }

        $contest = $models->first();

        if (! $sender->canSend($contest)) {
            return Action::danger('Nothing to send from this contest.');
        }

        $sent = $sender->send($contest, $target)->count();

        // Open the Works list on what was just sent, via the "Sent from" filter
        return Action::visit('/resources/works', [
            'works_filter' => self::sentFromFilter($contest->id),
        ])->withMessage("Sent {$sent} winning works to {$target->name}.");
    }

    /** The Works index filter query value Nova reads: base64 JSON of [{filter class: value}]. */
    public static function sentFromFilter(int $contestId): string
    {
        return base64_encode(json_encode([[SentFromContestFilter::class => $contestId]]));
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
