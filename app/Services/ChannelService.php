<?php

namespace App\Services;

use App\Channel;
use App\Medias;
use App\Plan;
use App\Services\ThumbService;
use App\User;
use Carbon\Carbon;

class ChannelService
{
    /**
     * Newly registered channel is 0 by default
     */
    protected const _CHANNEl_FREE = 0;

    /**
     * First users and friends
     */
    protected const _CHANNEL_EARLY_BIRD = 1;

    /**
     * Paying clients
     */
    protected const _CHANNEl_PREMIUM = 2;
    protected const _CHANNEl_VIP = 3;

    /**
     * Number of episodes allowed by plan
     */
    protected const _FREE_PLAN_EPISODES_NUMBER_ALLOWED_IN_PODCAST = 2;
    protected const _PREMIUM_PLAN_EPISODES_NUMBER_ALLOWED_IN_PODCAST = 10;
    protected const _EARLY_AND_VIP_PLAN_EPISODES_NUMBER_ALLOWED_IN_PODCAST = 33;

    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @params Channel $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(Channel $channel)
    {

        $monthBeginning = carbon::createMidnightDate(date('Y'), date('m'), 1);
        $monthEnding = carbon::create()->endOfMonth();

        $nbMediasGrabbedThisMonth = Medias::grabbedBetween($monthBeginning, $monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();

        return $nbMediasGrabbedThisMonth;
    }

    /**
     * This function will return the maximum number of episodes one podcast may include by type.
     * @params Channel $channel_type the kind of channel (free/premium/etc)
     * @return int maximum number of episode by month for its type
     */
    public static function getMaximumNumberOfEpisodeByMonthAndType(Channel $channel)
    {
        /**
         * Rechercher
         */
        switch ($channel->channel_premium) {
            case self::_CHANNEl_FREE:
                return self::_FREE_PLAN_EPISODES_NUMBER_ALLOWED_IN_PODCAST;
                break;
            case self::_CHANNEl_PREMIUM:
                return self::_PREMIUM_PLAN_EPISODES_NUMBER_ALLOWED_IN_PODCAST;
                break;
            case self::_CHANNEL_EARLY_BIRD:
            case self::_CHANNEl_VIP:
                return self::_EARLY_AND_VIP_PLAN_EPISODES_NUMBER_ALLOWED_IN_PODCAST;
                break;
        }
    }

    /**
     * This function will retrieve all user's channels.
     * @param User $user the user we need channels
     * @return channels models with thumb/vignette
     */
    public static function getAuthenticatedUserChannels(User $user)
    {
        $channels = $user->channels;
        foreach ($channels as $channel) {

            $channel->nbEpisodesGrabbedThisMonth = self::getNbEpisodesAlreadyDownloadedThisMonth($channel);
            $channel->nbEpisodesAllowedThisMonth = self::getMaximumNumberOfEpisodeByMonthAndType($channel);

            /**
             * If podcast has a thumb
             */
            $vignetteObtained = false;

            if ($thumb = $channel->thumbs) {
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

    /**
     * This function will return true if channel has no subscription.
     * If no subscription it means that channel is free.
     *
     * @param Channel $channel
     * @return boolean
     */
    public static function isFreeChannel(Channel $channel)
    {
        return App\Subscription::where('channel_id', $channel->channel_id)->doesntExist();
    }

}
