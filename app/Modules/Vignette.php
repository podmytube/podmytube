<?php

namespace App\Modules;

use App\Exceptions\VignetteCreationFromMissingThumbException;
use App\Thumb;
use Image;
use Illuminate\Support\Facades\Storage;

class Vignette extends Thumb
{
    /** @var string Vignette suffix */
    public const _VIGNETTE_SUFFIX = '_vig';

    /** @var string default vignette filename */
    public const _DEFAULT_VIGNETTE_FILE = 'default_vignette.jpg';

    /** @var integer default vignette width in pixels */
    public const _DEFAULT_VIGNETTE_WIDTH = 300;

    /**
     * This function will instantiate vignette object from the thumb one.
     */
    public static function fromThumb(Thumb $thumb)
    {
        return new static($thumb);
    }

    /**
     * 
     */
    private function __construct(Thumb $thumb)
    {
        $this->thumb = $thumb;
        $this->setFileName();
        $this->file_disk = $this->thumb->fileDisk();
        $this->channel_id = $this->thumb->channelId();
    }

    public function setFileName()
    {
        list($fileName, $fileExtension) = explode('.', $this->thumb->fileName());
        $this->file_name = $fileName . self::_VIGNETTE_SUFFIX . '.' . $fileExtension;
    }

    /**
     * This function is returning the vignette relative file path.
     * Something like [CHANNEL]/[THUMB_FILE_NAME]_vig.[THUMB_FILE_EXT]
     *
     * @return string vignette file name
     */
    public function relativePath()
    {
        list($fileName, $fileExtension) = explode('.', $this->thumb->fileName());
        return $this->channelPath() . $fileName . self::_VIGNETTE_SUFFIX . '.' . $fileExtension;
    }

    /**
     * This function will tell if vignette file does exist
     * 
     * @return bool true if file exists.
     */
    public function exists()
    {
        return Storage::disk($this->file_disk)->exists($this->relativePath());
    }


    public function make()
    {
        /** Verifying thumb file exists */
        if (!$this->thumb->exists()) {
            throw new VignetteCreationFromMissingThumbException(
                "Thumb file {{ " . parent::relativePath() . " }} for channel {{$this->channel_id}} is missing."
            );
        }

        /** getting data and convert it to an image object */
        $image = Image::make(parent::getData());

        /** creating vignette */
        $image->fit(
            self::_DEFAULT_VIGNETTE_WIDTH,
            self::_DEFAULT_VIGNETTE_WIDTH,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );

        /** Storing it */
        Storage::disk($this->file_disk)->put($this->relativePath(), (string) $image->encode());
    }
}
