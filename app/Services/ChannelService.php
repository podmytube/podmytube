<?php

namespace App\Services;

use App\Plan;
use App\Services\SubscriptionService;
use App\Services\ThumbService;
use App\Services\MediaService;
use App\User;
use App\Thumb;

class ChannelService
{
    /**
     * This function will retrieve all user's channels.
     * @param User $user the user we need channels
     * @return channels models with thumb/vignette
     * @todo should send an alert email to me
     */
    public static function getAuthenticatedUserChannels(User $user)
    {
        /**
         * getting users channel(s)
         */

        $channels = $user->channels;
        if ($channels->isEmpty()) {
            throw new \Exception("User {$user->user_id} has no channel.");
        }

        foreach ($channels as $channel) {
            $nbEpisodesGrabbedThisMonth = MediaService::getNbEpisodesAlreadyDownloadedThisMonth($channel);
            try {
                $subscription = SubscriptionService::getActiveSubscription($channel);
                $episodesPerMonth = $subscription->plan->nb_episodes_per_month;
            } catch (\Exception $e) {
                $episodesPerMonth = (Plan::find(Plan::_DEFAULT_PLAN_ID))->nb_episodes_per_month;
                /**
                 * @todo should send an alert email to me
                 */
            }
            $channel->isQuotaExceeded = $nbEpisodesGrabbedThisMonth >= $episodesPerMonth ? true : false;

            /**
             * If channel has a thumb
             */
            try {
                if ($channel->thumb) {
                    if ($channel->thumb->vignetteExists()) {
                        $channel->vigUrl = $channel->thumb->vignetteUrl();
                    } else {
                        if (ThumbService::createThumbVig($channel->thumb)) {
                            $channel->vigUrl = $channel->thumb->vignetteUrl();
                        }
                    }
                } else {
                    $channel->vigUrl = Thumb::defaultVignetteUrl();
                }
            } catch (\Exception $e) {
                1;
            }
        }
        return $channels;
    }
}
