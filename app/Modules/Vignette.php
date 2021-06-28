<?php

declare(strict_types=1);

namespace App\Modules;

use App\Exceptions\VignetteCreationFromMissingThumbException;
use App\Thumb;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class Vignette
{
    public const LOCAL_STORAGE_DISK = 'vignettes';
    public const VIGNETTE_SUFFIX = '_vig';
    public const DEFAULT_VIGNETTE_FILE = 'default_vignette.jpg';

    /** @var \App\Thumb used to create vignette */
    protected $thumb;

    /** @var \Intervention\Image\Image */
    protected $image;

    /**
     * private constructor.
     */
    private function __construct(Thumb $thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * This function will instantiate vignette object from the thumb one.
     */
    public static function fromThumb(Thumb $thumb)
    {
        return new static($thumb);
    }

    /**
     * This function will return the relative path to access the vignette.
     * Relative path is defined from the root path of the Storage(object) root.
     *
     * @return string something like UC0NCbj8CxzeCGIF6sODJ-7A/YItR6zUPAuQg1c2sJhStyZApgJkdeObVoPp4e7BQ.jpeg
     */
    public function relativePath(): string
    {
        return $this->thumb->coverable->channelId().'/'.$this->fileName();
    }

    /**
     * Return the fileName.
     *
     * @return string filename (with ext) of the vignette
     */
    public function fileName(): string
    {
        $pathParts = pathinfo($this->thumb->fileName());

        return $pathParts['filename'].self::VIGNETTE_SUFFIX.'.'.$pathParts['extension'];
    }

    /**
     * Tell if vignette exists.
     *
     * @return bool true if vignette exists. False else
     */
    public function exists()
    {
        return Storage::disk(self::LOCAL_STORAGE_DISK)
            ->exists($this->relativePath())
        ;
    }

    /**
     * Will return the internal url else return the default one.
     *
     * @return string thumb url to be used in the dashboard
     */
    public function url()
    {
        return Storage::disk(self::LOCAL_STORAGE_DISK)->url($this->relativePath());
    }

    /**
     * This function will create the vignette from the thumb.
     */
    public function makeIt()
    {
        // Verifying thumb file exists
        if (!$this->thumb->exists()) {
            throw new VignetteCreationFromMissingThumbException(
                "Thumb file {$this->thumb->relativePath()} for coverable {$this->thumb->coverableLabel()} is missing."
            );
        }

        // getting data and convert it to an image object
        $this->image = Image::make($this->thumb->getData());

        // creating vignette
        $this->image->fit(
            config('app.vignette_width'),
            config('app.vignette_height'),
            function ($constraint): void {
                $constraint->aspectRatio();
            }
        );

        return $this;
    }

    public function saveLocally()
    {
        Storage::disk(self::LOCAL_STORAGE_DISK)
            ->put($this->relativePath(), (string) $this->image->encode())
        ;

        return $this;
    }

    /**
     * This function is returning the data of the vignette.
     *
     * @return string content of the file
     */
    public function getData()
    {
        return (string) $this->image->encode();
    }

    /**
     * Should be done within a queue.
     */
    public function delete()
    {
        try {
            // removing local vig
            Storage::disk($this->thumb->fileDisk())->delete(
                $this->relativePath()
            );
            // removing local vig
            Storage::disk(self::REMOTE_STORAGE_DISK)->delete(
                $this->relativePath()
            );
        } catch (Exception $exception) {
            Log::alert(
                'Deleting vignette '.
                    $this->relativePath().
                    " has failed with message {{$exception->getMessage()}}."
            );

            throw $exception;
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
        return env('THUMBS_URL').'/'.self::DEFAULT_VIGNETTE_FILE;
    }

    public function localFilePath()
    {
        return Storage::disk(self::LOCAL_STORAGE_DISK)->path($this->relativePath());
    }

    public function remoteFilePath()
    {
        return config('app.thumbs_path').$this->relativePath();
    }
}
