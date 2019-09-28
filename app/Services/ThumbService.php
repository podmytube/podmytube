<?php

namespace App\Services;

use App\Channel;
use App\Thumb;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class ThumbService
{
    /**
     * This function is checking is thumb folder exist for current Thumb model.
     */
    public static function thumbExists(Thumb $thumb)
    {
        if (!self::pathExists(self::getThumbFilePath($thumb))) {
            throw new \Exception("This channel {$thumb->channel_id} has no thumb !");
        }
        return true;
    }

    /**
     * This function will tell if there is really a thumb on this path.
     * 
     * @param string $thumbPath th e thumb path to check.
     * @return boolean true if exists
     */
    public static function pathExists($thumbPath)
    {
        return Storage::disk(Thumb::_STORAGE_DISK)->exists($thumbPath);
    }

    /**
     * return the url of a default thumb if user doesn't upload any.
     * @return string the url of the default thumb
     */
    public static function getDefaultThumbUrl()
    {
        return getenv('THUMBS_URL') . '/' . Thumb::_DEFAULT_THUMB_FILE;
    }

    /**
     * return the url of a default vignette if user doesn't upload any.
     * @return string the url of the default vignette
     */
    public static function getDefaultVignetteUrl()
    {
        return getenv('THUMBS_URL') . '/' . Thumb::_DEFAULT_VIGNETTE_FILE;
    }

    /**
     * return the url of the thumbs for the current channel.
     * @param Channel $channel Channel model
     */
    public static function getChannelThumbUrl(Channel $channel)
    {
        /**
         * If channel has no thumb => returning default one
         */
        if (empty($channel->thumb)) {
            return self::getDefaultThumbUrl();
        }

        $thumb = $channel->thumb;
        /**
         * If channel has a database entry into thumbs but no files => returning default one
         */
        try {
            self::thumbExists($thumb);
        } catch (\Exception $e) {
            Log::error("Channel {{$channel->channel_id}} has an entry in database but no file is thumb folder.");
            return self::getDefaultThumbUrl();
        }

        if (!Storage::disk($thumb->file_disk)
            ->exists($thumb->channel_id . DIRECTORY_SEPARATOR . $thumb->file_name)) {
            throw new \Exception("Thumb for this channel {$thumb->channel_id} does not exist");
        }

        return Storage::disk($thumb->file_disk)->url($thumb->channel_id . '/' . $thumb->file_name);
    }

    /**
     * Get the channel vignette url for the specified thumb.
     * @param Thumb $thumb the thumb model to retrieve
     */
    public static function getChannelVignetteUrl(Thumb $thumb)
    {
        try {
            self::thumbExists($thumb);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $vignettePath = self::getVignetteFilePath($thumb);

        if (!Storage::disk($thumb->file_disk)->exists($vignettePath)) {
            throw new \Exception('Thumb for this channel does not exist');
        }

        return Storage::disk($thumb->file_disk)->url($vignettePath);
    }

    /**
     * From a Thumb model will return the thumb file path.
     * Typically will return something like
     * UC9hHeywcPBnLglqnQRaNShQ/Qb3mks0ghLSKQBpLOHNz5gY850ZgFAetkIodFI2K.png
     * @param Thumb $thumb
     */
    public static function getThumbFilePath(Thumb $thumb)
    {
        return $thumb->channel_id . DIRECTORY_SEPARATOR . $thumb->file_name;
    }

    /**
     * From a Thumb model will return the vignette file path.
     * Typically will return something like
     * UC9hHeywcPBnLglqnQRaNShQ/Qb3mks0ghLSKQBpLOHNz5gY850ZgFAetkIodFI2K_vig.png
     * @param Thumb $thumb
     * @return string the vignette file path
     */
    public static function getVignetteFilePath(Thumb $thumb)
    {
        $fileInfos = pathinfo(self::getThumbFilePath($thumb));
        return $thumb->channel_id . DIRECTORY_SEPARATOR . $fileInfos['filename'] . '_vig' . '.' . $fileInfos['extension'];
    }

    /**
     * This function will create a vignette from the podcast thumbnail.
     * @param Thumb $thumb
     */
    public static function createThumbVig(Thumb $thumb)
    {
        // mini thumb to be used in dashboard creation
        $thumbPath = self::getThumbFilePath($thumb);
        $vignettePath = self::getVignetteFilePath($thumb);

        /**
         * Grabbing thumb file (if exists)
         */
        if (!Storage::disk($thumb->file_disk)->exists($thumbPath)) {
            throw new \Exception("Thumb file {$thumbPath} does not exist");
        }

        //$thumbFullPath = Storage::disk($thumb->file_disk)->path($thumbPath);

        /**
         * Getting Thumb data
         */
        $thumbData = Storage::disk($thumb->file_disk)->get($thumbPath);

        /**
         * Converting it as an image
         */
        $thumbnail = Image::make($thumbData);
        //$thumbnail = Image::make($thumbFullPath);

        /**
         * creating vignette
         */
        $thumbnail->fit(
            Thumb::_DEFAULT_VIGNETTE_FILE,
            Thumb::_DEFAULT_VIGNETTE_FILE,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );

        /**
         * Storing it
         */
        Storage::disk($thumb->file_disk)->put($vignettePath, (string) $thumbnail->encode());

        /**
         * Return full path of the vig
         */
        return Storage::disk($thumb->file_disk)->path($vignettePath);
    }
}
