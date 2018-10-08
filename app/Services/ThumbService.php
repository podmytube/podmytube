<?php

namespace App\Services;

use App\Thumbs;

class ThumbService
{
    protected const STORAGE_THUMB_DISK = 'thumbs';

    protected const VIGNETTE_FILENAME = 'dashboard_thumb.jpg';

    /**
     * return the url of the thumbs for the current channel.
     * @param Thumbs $channel Channel model
     */

    public function getChannelThumb(Thumbs $thumb)
    {

        if (!\Storage::disk(self::STORAGE_THUMB_DISK)
            ->exists($thumb->channel_id . '/' . $thumb->file_name)) {

            throw new \Exception('Thumbs for this channel does not exist');

        }

        return \Storage::disk(self::STORAGE_THUMB_DISK)->url($thumb->channel_id . '/' . $thumb->file_name);
    }

    /**
     * Get the channel vignette.
     * @param Thumbs 
     */

    public function getChannelVignette(Thumbs $thumb)
    {

        if (!\Storage::disk(self::STORAGE_THUMB_DISK)
            ->exists($thumb->channel_id . '/' . self::VIGNETTE_FILENAME)) {

            throw new \Exception('Thumbs for this channel does not exist');

        }

        return \Storage::disk(self::STORAGE_THUMB_DISK)->url($thumb->channel_id . '/' . self::VIGNETTE_FILENAME);
    }

}