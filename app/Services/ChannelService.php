<?php

namespace App\Services;

use App\Channel;
use App\User;
use App\Medias;

use Carbon\Carbon;
use App\Services\ThumbService;
use Symfony\Component\HttpFoundation\Request;

class ChannelService
{

    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @params string $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public static function getNbEpisodesAlreadyDownloadedThisMonth(Channel $channel)
    {
    
        //$monthBeginning = carbon::createMidnightDate(date('Y'), date('m'), 1);
        $monthBeginning = carbon::createMidnightDate(date('Y'), 1, 1);
        $monthEnding = carbon::create()->endOfMonth();
        
        $nbMediasGrabbedThisMonth = Medias::grabbedBetween($monthBeginning, $monthEnding)
            ->whereNotNull('grabbed_at')
            ->where('channel_id', $channel->channel_id)
            ->count();

        return $nbMediasGrabbedThisMonth;
    }

    /**
     * This function will retrieve all user's channels.
     * @param User $user the user we need channels
     * @return channels models with thumb/vignette
     */
    public static function getAuthenticatedUserChannels(User $user)
    {
        $channels = $user->channels;
		foreach($channels as $channel) {
            /**
             * Retrieve thumbs relation
             */
            $thumb = $channel->thumbs;

            $channel->nbEpisodesGrabbedThisMonth = self::getNbEpisodesAlreadyDownloadedThisMonth($channel);
            $channel->nbEpisodesAllowedThisMonth = -1;

            /**
             * Getting vignette
             */
            try {
                $channel->isDefaultVignette = false;
                $channel->vigUrl = ThumbService::getChannelVignetteUrl($thumb);
            } catch (\Exception $e) {
                $channel->isDefaultVignette = true;
                $channel->vigUrl = ThumbService::getDefaultVignetteUrl();
            }
        }

        return $channels;

    }

}