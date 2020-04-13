<?php

namespace App\Modules;

use App\Exceptions\VignetteCreationFromMissingThumbException;
use App\Exceptions\VignetteCreationFromThumbException;
use App\Exceptions\VignetteUploadException;
use App\Thumb;
use Illuminate\Support\Facades\Storage;
use Image;

class Vignette
{
    /** @var string _REMOTE_STORAGE_DISK where thumbs and vigs are stored remotely */
    public const _REMOTE_STORAGE_DISK = 'sftpthumbs';

    /** @var string Vignette suffix */
    public const _VIGNETTE_SUFFIX = '_vig';

    /** @var string default vignette filename */
    public const _DEFAULT_VIGNETTE_FILE = 'default_vignette.jpg';

    /** @var integer default vignette width in pixels */
    public const _DEFAULT_VIGNETTE_WIDTH = 300;

    /** @var thumb used to create vignette */
    protected $thumb;

    /**
     * This function will instantiate vignette object from the thumb one.
     */
    public static function fromThumb(Thumb $thumb)
    {
        return new static($thumb);
    }

    /**
     * private constructor
     */
    private function __construct(Thumb $thumb)
    {
        $this->thumb = $thumb;
        $this->setFileName();
        $this->setChannelId();
    }

    /**
     * This function will return the relative path to access the vignette.
     * Relative path is defined from the root path of the Storage(object) root.
     *
     * @return string something like UC0NCbj8CxzeCGIF6sODJ-7A/YItR6zUPAuQg1c2sJhStyZApgJkdeObVoPp4e7BQ.jpeg
     */
    public function relativePath(): string
    {
        return $this->channelId() . '/' . $this->fileName();
    }

    /**
     * This will obtain the channel_id from the thumb.
     */
    protected function setChannelId()
    {
        $this->channel_id = $this->thumb->channelId();
    }

    /**
     * Return the channel_id.
     *
     * @return string channel_id of the vignette
     */
    public function channelId(): string
    {
        return $this->channel_id;
    }

    /**
     * This will obtain the filename of the thumb and set the filename property for the vignette.
     */
    protected function setFileName()
    {
        list($fileName, $fileExtension) = explode(
            '.',
            $this->thumb->fileName()
        );
        $this->file_name =
            $fileName . self::_VIGNETTE_SUFFIX . '.' . $fileExtension;
    }

    /**
     * Return the fileName.
     *
     * @return string filename (with ext) of the vignette
     */
    public function fileName(): string
    {
        return $this->file_name;
    }

    /**
     * Tell if vignette exists.
     *
     * @return bool true if vignette exists. False else
     */
    public function exists()
    {
        return Storage::disk($this->thumb->fileDisk())->exists(
            $this->relativePath()
        );
    }

    /**
     * Will return the internal url else return the default one.
     *
     * @return string thumb url to be used in the dashboard
     */
    public function url()
    {
        return Storage::disk($this->thumb->file_disk)->url(
            $this->relativePath()
        );
    }

    /**
     * This function will create the vignette from the thumb.
     */
    public function makeIt()
    {
        /** Verifying thumb file exists */
        if (!$this->thumb->exists()) {
            throw new VignetteCreationFromMissingThumbException(
                'Thumb file { ' .
                    $this->thumb->relativePath .
                    " } on disk {$this->thumb->file_disk} for channel {$this->channel_id} is missing."
            );
        }
        try {
            /** getting data and convert it to an image object */
            $image = Image::make($this->thumb->getData());

            /** creating vignette */
            $image->fit(
                self::_DEFAULT_VIGNETTE_WIDTH,
                self::_DEFAULT_VIGNETTE_WIDTH,
                function ($constraint) {
                    $constraint->aspectRatio();
                }
            );

            /** Storing it locally */
            Storage::disk($this->thumb->fileDisk())->put(
                $this->relativePath(),
                (string) $image->encode()
            );
        } catch (\Exception $exception) {
            throw new VignetteCreationFromThumbException(
                "Creation of vignette from thumb {{$this->thumb}} for channel {{$this->thumb->channel_id}} has failed with message :" .
                    $e->getMessage()
            );
        }
        return $this;
    }

    /**
     * This function is returning the data of the vignette.
     *
     * @return string content of the file.
     */
    public function getData()
    {
        return Storage::disk($this->thumb->fileDisk())->get(
            $this->relativePath()
        );
    }

    /**
     * This function will upload the vignette.
     */
    public function upload()
    {
        try {
            Storage::disk(self::_REMOTE_STORAGE_DISK)->put(
                $this->relativePath(),
                $this->getData()
            );

            /** Once uploaded, we are setting the channel_path on the remote to public visibility  */
            Storage::disk(self::_REMOTE_STORAGE_DISK)->setVisibility(
                $this->channelId(),
                'public'
            );
        } catch (\Exception $exception) {
            $message =
                "Uploading vignette {{$this->fileName()}} to remote has failed with message : " .
                $e->getMessage();
            Log::alert($message);
            throw new VignetteUploadException($message);
        }
        return true;
    }

    /**
     * Should be done within a queue.
     */
    public function delete()
    {
        try {
            /** removing local vig */
            Storage::disk($this->thumb->fileDisk())->delete(
                $this->relativePath()
            );
            /** removing local vig */
            Storage::disk(self::_REMOTE_STORAGE_DISK)->delete(
                $this->relativePath()
            );
        } catch (\Exception $exception) {
            Log::alert(
                'Deleting vignette ' .
                    $this->relativePath() .
                    " has failed with message {{$e->getMessage()}}."
            );
            throw $e;
        }
        return true;
    }

    /**
     * return the url of the default vignette.
     *
     * @return string default vignette url to be used in the dashboard
     */
    public static function defaultUrl()
    {
        return env('THUMBS_URL') . '/' . self::_DEFAULT_VIGNETTE_FILE;
    }
}
