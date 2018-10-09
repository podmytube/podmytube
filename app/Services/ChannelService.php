<?php

namespace App\Services;

use App\Channel;
use App\User;

use App\Services\ThumbService;

class ChannelService
{

    /**
     * This function will return the number of episodes already grabbed for one channel.
     * @params string $channel_id the channel
     * @return int the number of episodes grabbed this month for this channel
     */
    public function getNbEpisodesAlreadyDownloadedThisMonth(Channel $channel)
    {
    
        $monthBeginning = carbon::createMidnightDate(date('Y'), date('m'), 1);
        $monthEnding = carbon::create()->endOfMonth();
        
        $results = \DB::queryFirstRow("
            SELECT count(*) as nbMediasGrabbed
            FROM medias
            WHERE channel_id = %s
            AND grabbed_at between %t AND %t",
            $channel_id, 
            $monthBeginning->format('Y-m-d'),
            $monthEnding->format('Y-m-d')
        );

        if (\DB::count() <= 0) {
            
            throw new \Exception("Channel id {$channel_id} has no grabbed media this month.");

        }

        return $results['nbMediasGrabbed'];
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