<?php

namespace App\Services;

use App\Thumbs;

use Image;

class ThumbService
{
    protected const DEFAULT_THUMB_DISK = 'thumbs';
    protected const DEFAULT_THUMB_FILE = 'default_thumb.jpg';
    protected const DEFAULT_VIGNETTE_FILE = 'default_vignette.jpg';
    protected const VIGNETTE_FILENAME = 'dashboard_thumb.jpg';
    protected const VIGNETTE_WIDTH = 300;


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
     * return the url of a default thumb if user doesn't upload any.
     * @return string the url of the default thumb
     */
    public static function getDefaultThumbUrl()
    {
        if (!\Storage::disk(self::DEFAULT_THUMB_DISK)->exists(self::DEFAULT_THUMB_FILE)) {
            throw new \Exception("Default thumb {" . self::DEFAULT_THUMB_FILE . "} does not exist on this server !");
        }
        return \Storage::disk(self::DEFAULT_THUMB_DISK)->url(self::DEFAULT_THUMB_FILE);
    }
    

    /**
     * return the url of a default vignette if user doesn't upload any.
     * @return string the url of the default vignette
     */
    public static function getDefaultVignetteUrl()
    {
        if (!\Storage::disk(self::DEFAULT_THUMB_DISK)->exists(self::DEFAULT_VIGNETTE_FILE)) {
            throw new \Exception("Default vignette {" . self::DEFAULT_VIGNETTE_FILE . "} does not exist on this server !");
        }
        return \Storage::disk(self::DEFAULT_THUMB_DISK)->url(self::DEFAULT_VIGNETTE_FILE);
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
            throw new \Exception($e->getMessage());
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

    public static function getChannelVignetteUrl(Thumbs $thumb)
    {
        try {
            self::thumbFolderExists($thumb);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $vignettePath = self::getVignetteFilePath($thumb);

        if (!\Storage::disk($thumb->file_disk)->exists($vignettePath)) {
            throw new \Exception('Thumbs for this channel does not exist');
        }

        return \Storage::disk($thumb->file_disk)->url($vignettePath);
    }

    /**
     * From a Thumb model will return the thumb file path.
     * Typically will return something like 
     * UC9hHeywcPBnLglqnQRaNShQ/Qb3mks0ghLSKQBpLOHNz5gY850ZgFAetkIodFI2K.png
     * @param Thumbs $thumb 
     
     */
    public static function getThumbFilePath(Thumbs $thumb)
    {
        return $thumb->channel_id . DIRECTORY_SEPARATOR . $thumb->file_name;
    }

    /**
     * From a Thumb model will return the vignette file path.
     * Typically will return something like 
     * UC9hHeywcPBnLglqnQRaNShQ/Qb3mks0ghLSKQBpLOHNz5gY850ZgFAetkIodFI2K_vig.png
     * @param Thumbs $thumb 
     * @return string the vignette file path
     */
    public static function getVignetteFilePath(Thumbs $thumb)
    {
        $thumbPath = self::getThumbFilePath($thumb);
        $fileInfos = pathinfo($thumbPath);
        return $thumb->channel_id . DIRECTORY_SEPARATOR . $fileInfos['filename'].'_vig'. '.' . $fileInfos['extension'];
    }

    /**
     * This function will create a vignette from the podcast thumbnail.
     * @param Thumb $thumb 
     */
    public static function createThumbVig(Thumbs $thumb)
    {
        // mini thumb to be used in dashboard creation
        $thumbPath = self::getThumbFilePath($thumb);
        $vignettePath = self::getVignetteFilePath($thumb);
        
        /**
         * Grabbing thumb file (if exists)
         */
        if (!\Storage::disk($thumb->file_disk)->exists($thumbPath)) {
            throw new \Exception("Thumb file {$thumbPath} does not exist");
        }
        
        /**
         * Getting Thumb data
         */
        $thumbData = \Storage::disk($thumb->file_disk)->get($thumbPath);

        /**
         * Converting it as an image
         */
        $thumbnail = Image::make($thumbData);

        /**
         * creating vignette
         */
        $thumbnail->fit(
            self::VIGNETTE_WIDTH,
            self::VIGNETTE_WIDTH,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );

        /**
         * Storing it
         */
        \Storage::disk($thumb->file_disk)->put($vignettePath, (string)$thumbnail->encode());

        return true;
    }

}