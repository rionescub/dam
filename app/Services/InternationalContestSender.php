<?php

namespace App\Services;

use App\Models\Contest;
use App\Models\Diploma;
use App\Models\Team;
use App\Models\Work;
use App\Models\WorkDetails;
use App\Scopes\ContestTeam;
use App\Scopes\CurrentTeam;
use App\Scopes\WorkTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Clones the rank 1 works of finished national contests into the open EN contest.
 *
 * Team scopes are dropped on purpose: a national admin sends into the EN team's contest.
 */
class InternationalContestSender
{
    private ?Team $enTeam = null;

    private ?Contest $target = null;

    private bool $targetLoaded = false;

    public function enTeam(): ?Team
    {
        return $this->enTeam ??= Team::where('link', 'en')->first();
    }

    /** The EN contest winners go to: the next one whose ceremony hasn't happened yet. */
    public function targetContest(): ?Contest
    {
        if (! $this->targetLoaded) {
            $this->targetLoaded = true;
            $this->target = $this->enTeam()
                ? Contest::withoutGlobalScope(CurrentTeam::class)
                    ->where('team_id', $this->enTeam()->id)
                    ->where('ceremony_date', '>', now())
                    ->orderBy('ceremony_date')
                    ->first()
                : null;
        }

        return $this->target;
    }

    /**
     * Non-EN contests of this year whose ceremony has passed.
     *
     * @param  array<int|string>  $ids  only these contests, when given
     * @return Collection<int, Contest>
     */
    public function finishedContests(array $ids = []): Collection
    {
        return $this->finished(Contest::withoutGlobalScope(CurrentTeam::class))
            ->when($ids !== [], fn (Builder $query) => $query->whereKey($ids))
            ->orderBy('id')
            ->get();
    }

    public function isFinished(Contest $contest): bool
    {
        return $this->finished(Contest::withoutGlobalScope(CurrentTeam::class))->whereKey($contest->id)->exists();
    }

    /** contest:update-results gives every work a diploma; without them every work is still on the default rank 1. */
    public function isRanked(Contest $contest): bool
    {
        return Diploma::withoutGlobalScope(ContestTeam::class)->where('contest_id', $contest->id)->exists();
    }

    /**
     * Every rank 1 work with details, so works tied for first place in a category all go.
     *
     * @return Collection<int, Work>
     */
    public function winners(Contest $contest): Collection
    {
        return Work::withoutGlobalScope(ContestTeam::class)
            ->with(['details' => fn ($query) => $query->withoutGlobalScope(WorkTeam::class)])
            ->where('contest_id', $contest->id)
            ->where('rank', 1)
            ->whereHas('details', fn ($query) => $query->withoutGlobalScope(WorkTeam::class))
            ->get();
    }

    /**
     * Winners not in the target contest yet. Deleted clones count as sent, so a winner removed from the EN contest isn't sent again.
     *
     * @return Collection<int, Work>
     */
    public function unsentWinners(Contest $contest, Contest $target): Collection
    {
        $sent = Work::withoutGlobalScope(ContestTeam::class)
            ->withTrashed()
            ->where('contest_id', $target->id)
            ->whereNotNull('source_work_id')
            ->pluck('source_work_id');

        return $this->winners($contest)->reject(fn (Work $work) => $sent->contains($work->id))->values();
    }

    /** Finished, ranked, and with at least one winner not sent yet. */
    public function canSend(Contest $contest): bool
    {
        $target = $this->targetContest();

        return $target !== null
            && $this->isFinished($contest)
            && $this->isRanked($contest)
            && $this->unsentWinners($contest, $target)->isNotEmpty();
    }

    /**
     * Clone the contest's unsent winners into the target contest.
     *
     * @return Collection<int, Work> the clones, each with its details loaded
     */
    public function send(Contest $contest, Contest $target): Collection
    {
        $now = now();

        return $this->unsentWinners($contest, $target)->map(function (Work $work) use ($target, $now): Work {
            return DB::transaction(function () use ($work, $target, $now): Work {
                $clonedWork = $work->replicate();
                $clonedWork->contest_id = $target->id;
                $clonedWork->source_work_id = $work->id;
                $clonedWork->created_at = $now;
                $clonedWork->updated_at = $now;
                $clonedWork->save();

                /** @var WorkDetails $clonedDetails */
                $clonedDetails = $work->details->replicate();
                $clonedDetails->work_id = $clonedWork->id;
                $clonedDetails->created_at = $now;
                $clonedDetails->updated_at = $now;
                $clonedDetails->save();

                return $clonedWork->setRelation('details', $clonedDetails);
            });
        });
    }

    private function finished(Builder $query): Builder
    {
        return $query
            ->whereYear('start_date', now()->year)
            ->where('ceremony_date', '<', now())
            ->when($this->enTeam(), fn (Builder $query, Team $enTeam) => $query->where('team_id', '!=', $enTeam->id));
    }
}
