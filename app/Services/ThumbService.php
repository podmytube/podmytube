<?php

namespace App\Services;

use App\Thumbs;

class ThumbService
{
    protected const VIGNETTE_FILENAME = 'dashboard_thumb.jpg';

    /**
     * This function is checking is thumb folder exist for current Thumb model.
     */
    public static function thumbFolderExists(Thumbs $thumb)
    {

        if (!\Storage::disk($thumb->file_disk)->exists($thumb->channel_id . '/' . $thumb->file_name)) {

            throw new \Exception("This channel {$thumb->channel_id} has no thumb folder !");

        }
        return true;

    }

    /**
     * return the url of the thumbs for the current channel.
     * @param Thumbs $channel Channel model
     */
    public static function getDefaultThumbUrl(Thumbs $thumb)
    {

        if (!\Storage::disk($thumb->file_disk)->exists(self::VIGNETTE_FILENAME)) {

            throw new \Exception("Default thumb {".self::VIGNETTE_FILENAME."} does not exist on this server !");

        }

        return \Storage::disk($thumb->file_disk)->url(self::VIGNETTE_FILENAME);
    }

    /**
     * return the url of the thumbs for the current channel.
     * @param Thumbs $channel Channel model
     */
    public static function getChannelThumbUrl(Thumbs $thumb)
    {

        try {
            self::thumbFolderExists($thumb);
        } catch (\Exception $e) {
             throw new \Exception ($e->getMessage()) ;
        }

        if (!\Storage::disk($thumb->file_disk)
            ->exists($thumb->channel_id . '/' . $thumb->file_name)) {

            throw new \Exception("Thumbs for this channel {$thumb->channel_id} does not exist");

        }

        return \Storage::disk($thumb->file_disk)->url($thumb->channel_id . '/' . $thumb->file_name);
    }

    /**
     * Get the channel vignette.
     * @param Thumbs 
     */

    public function getChannelVignetteUrl(Thumbs $thumb)
    {

        try {
            self::thumbFolderExists($thumb);
        } catch (\Exception $e) {
             throw new \Exception ($e->getMessage()) ;
        }

        if (!\Storage::disk(self::STORAGE_THUMB_DISK)
            ->exists($thumb->channel_id . '/' . self::VIGNETTE_FILENAME)) {

            throw new \Exception('Thumbs for this channel does not exist');

        }

        return \Storage::disk(self::STORAGE_THUMB_DISK)->url($thumb->channel_id . '/' . self::VIGNETTE_FILENAME);
    }

}