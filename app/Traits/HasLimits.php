<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\ChannelHasNoSubscriptionException;
use App\Modules\PeriodsHelper;

/**
 * This trait is getting limit for one channel.
 * By limits I mean the one that he has subscribed for.
 */
trait HasLimits
{
    public function numberOfEpisodesAllowed(): int
    {
        if ($this->subscription === null) {
            throw new ChannelHasNoSubscriptionException("Channel {$this->nameWithId()} has no subscription.");
        }

        return $this->plan->nb_episodes_per_month;
    }

    public function numberOfEpisodesGrabbed(
        ?int $month = null,
        ?int $year = null
    ): int {
        $month = $month ?? intval(date('m'));
        $year = $year ?? intval(date('Y'));

        return $this->medias()
            ->whereBetween('grabbed_at', [
                PeriodsHelper::create($month, $year)->startDate(),
                PeriodsHelper::create($month, $year)->endDate(),
            ])
            ->count()
        ;
    }

    /**
     * Tell if channel has reached its limits.
     * Tell if channel has already grabbed all the episodes
     * its subscription is allowing it to.
     */
    public function hasReachedItslimit(
        ?int $month = null,
        ?int $year = null
    ): bool {
        return $this->numberOfEpisodesGrabbed($month, $year) >=
            $this->numberOfEpisodesAllowed();
    }
}
