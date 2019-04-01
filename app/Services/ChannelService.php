<?php

namespace App\Services;

use App\Channel;
use App\Medias;
use App\Plan;
use App\Services\SubscriptionService;
use App\Services\ThumbService;
use App\User;
use App\Log;
use Carbon\Carbon;

class ChannelService
{
   
    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @params Channel $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(Channel $channel)
    {

        $monthBeginning = carbon::createMidnightDate(date('Y'), date('m'), 1);
        $monthEnding = carbon::today()->endOfMonth();
        
        return Medias::grabbedBetween($monthBeginning, $monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();
    }

    /**
     * This function will retrieve all user's channels.
     * @param User $user the user we need channels
     * @return channels models with thumb/vignette
     * @todo should send an alert email to me
     */
    public static function getAuthenticatedUserChannels(User $user)
    {
        $channels = $user->channels;
        foreach ($channels as $channel) {

            $nbEpisodesGrabbedThisMonth = self::getNbEpisodesAlreadyDownloadedThisMonth($channel);
            try {
                $subscription = SubscriptionService::getActiveSubscription($channel);
                $episodesPerMonth=$subscription->plan->nb_episodes_per_month;
            } catch (\Exception $e) {
                $episodesPerMonth=(Plan::find(Plan::_DEFAULT_PLAN_ID))->nb_episodes_per_month;
                /**
                 * @todo should send an alert email to me
                 */                
            }

            $channel->isQuotaExceeded = $nbEpisodesGrabbedThisMonth >= $episodesPerMonth ? true : false;
                        
            /**
             * If podcast has a thumb
             */
            $vignetteObtained = false;

            if ($thumb = $channel->thumb) {
                try {
                    $channel->vigUrl = ThumbService::getChannelVignetteUrl($thumb);
                    $vignetteObtained = true;
                } catch (\Exception $e) {
                    /**
                     * No vignette may occur for early birds channel before the vignette creation.
                     * We are trying to create it. If we succeed we are using it.
                     */
                    try {
                        if (ThumbService::createThumbVig($thumb)) {
                            $channel->vigUrl = ThumbService::getChannelVignetteUrl($thumb);
                            $vignetteObtained = true;
                        }
                    } catch (\Exception $e) {
                        /**
                         * Doing nothing.
                         * This may occur if there is a thumb in database but files have been removed/moved.
                         */
                    }
                }
            }
            if (!$vignetteObtained) {
                $channel->isDefaultVignette = true;
                $channel->vigUrl = ThumbService::getDefaultVignetteUrl();
            } else {
                $channel->isDefaultVignette = false;
            }

        }

        return $channels;

    }


}
