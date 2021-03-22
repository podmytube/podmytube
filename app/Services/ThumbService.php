<?php

namespace App\Services;

use App\Channel;
use App\Exceptions\VignetteCreationFromMissingThumbException;
use App\Exceptions\VignetteCreationFromThumbException;
use App\Modules\Vignette;
use App\Thumb;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class ThumbService
{
    public static function create()
    {
        return new static();
    }

    /**
     * This function will store the uploaded file at its new location.
     *
     * @param UploadedFile $uploadedFile the file that has just been uploaded.
     * @param Channel      $channel      the channel that owns this thumb.
     *
     * @return String filePath
     */
    public function storeThumb(UploadedFile $uploadedFile, Channel $channel)
    {
        $filePath = $uploadedFile->store(
            $channel->channel_id,
            Thumb::LOCAL_STORAGE_DISK
        );
        return substr($filePath, strlen($channel->channel_id) + 1);
    }

    /**
     * This function will do the operations required when a new thumb is uploaded.
     *
     * @param UploadedFile $uploadedFile the file that has just been uploaded.
     * @param Channel      $channel      the channel that owns this thumb.
     */
    public function addUploadedThumb(
        UploadedFile $uploadedFile,
        Channel $channel
    ): bool {
        try {
            /**
             * Getting fileSize
             */
            $fileSize = $uploadedFile->getSize();

            /**
             * Store the uploaded file to its final location
             */
            $fileName = $this->storeThumb($uploadedFile, $channel);

            /**
             * Associate the thumb to the channel
             */
            Thumb::updateOrCreate(
                [
                    'channel_id' => $channel->channel_id,
                ],
                [
                    'channel_id' => $channel->channel_id,
                    'file_name' => $fileName,
                    'file_disk' => Thumb::LOCAL_STORAGE_DISK,
                    'file_size' => $fileSize,
                ]
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
        return true;
    }

    /**
     * This function is checking is thumb folder exist for current Thumb model.
     */
    public function thumbExists(Thumb $thumb)
    {
        if (!$this->pathExists($this->getThumbFilePath($thumb))) {
            throw new \Exception(
                "This channel {$thumb->channel_id} has no thumb !"
            );
        }
        return true;
    }

    /**
     * This function will tell if there is really a thumb on this path.
     *
     * @param string $thumbPath the thumb path to check.
     *
     * @return bool true if exists
     */
    public static function pathExists($thumbPath)
    {
        return Storage::disk(Thumb::LOCAL_STORAGE_DISK)->exists($thumbPath);
    }

    /**
     * return the url of a default thumb if user doesn't upload any.
     *
     * @return string the url of the default thumb
     */
    public static function getDefaultThumbUrl()
    {
        return config('app.thumbs_url') . '/' . Thumb::DEFAULT_THUMB_FILE;
    }

    /**
     * return the url of a default vignette if user doesn't upload any.
     *
     * @return string the url of the default vignette
     */
    public static function getDefaultVignetteUrl()
    {
        return config('app.thumbs_url') . '/' . Vignette::DEFAULT_VIGNETTE_FILE;
    }

    /**
     * return the url of the thumbs for the current channel.
     *
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
        } catch (\Exception $exception) {
            Log::error(
                "Channel {$channel->channel_id} has an entry in database but no file is thumb folder."
            );
            return self::getDefaultThumbUrl();
        }

        if (
            !Storage::disk($thumb->file_disk)->exists(
                $thumb->channel_id . '/' . $thumb->file_name
            )
        ) {
            throw new \Exception(
                "Thumb for this channel {$thumb->channel_id} does not exist"
            );
        }

        return Storage::disk($thumb->file_disk)->url(
            $thumb->channel_id . '/' . $thumb->file_name
        );
    }

    /**
     * Get the channel vignette url for the specified thumb.
     *
     * @param Thumb $thumb the thumb model to retrieve
     */
    public function getChannelVignetteUrl(Thumb $thumb)
    {
        try {
            self::thumbExists($thumb);
        } catch (\Exception $exception) {
            throw $exception;
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
     */
    public function getThumbFilePath()
    {
        return $this->channel_id . '/' . $this->file_name;
    }

    /**
     * From a Thumb model will return the vignette file path.
     * Typically will return something like
     * UC9hHeywcPBnLglqnQRaNShQ/Qb3mks0ghLSKQBpLOHNz5gY850ZgFAetkIodFI2K_vig.png
     *
     * @param Thumb $thumb
     *
     * @return string the vignette file path
     */
    public function getVignetteFilePath()
    {
        $fileInfos = pathinfo($this->getThumbFilePath());
        return $this->channel_id .
            '/' .
            $fileInfos['filename'] .
            '_vig.' .
            $fileInfos['extension'];
    }

    /**
     * This function will create a vignette from the podcast thumbnail.
     *
     * @param Channel $channel
     */
    public function createThumbVig(Channel $channel)
    {
        try {
            /**
             * Grabbing thumb file (if exists)
             */
            if (!$channel->thumb->exists()) {
                throw new VignetteCreationFromMissingThumbException(
                    "Thumb file for channel {{$channel->channel_id}} is missing."
                );
            }

            /**
             * Converting it as an image
             */
            $thumbnail = Image::make($channel->thumb->getData());

            /**
             * creating vignette
             */
            $thumbnail->fit(
                Thumb::DEFAULT_VIGNETTE_WIDTH,
                Thumb::DEFAULT_VIGNETTE_WIDTH,
                function ($constraint) {
                    $constraint->aspectRatio();
                }
            );

            /**
             * Storing it
             */
            Storage::disk($channel->thumb->file_disk)->put(
                $channel->thumb->vignetteRelativePath(),
                (string) $thumbnail->encode()
            );
        } catch (\Exception $exception) {
            throw new VignetteCreationFromThumbException(
                "Vignette creation for channel {{$channel->channel_id}} has failed with message : " .
                    $exception->getMessage()
            );
        }
        return true;
    }
}
