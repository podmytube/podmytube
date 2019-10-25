<?php

namespace App\Modules;

use Image;
use App\Thumb;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\VignetteCreationFromMissingThumbException;

class Vignette
{
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
     * 
     */
    private function __construct(Thumb $thumb)
    {
        $this->thumb = $thumb;
        $this->channel_id = 
        $this->setChannelId();
        $this->setFileDisk();
    }

    /**
     * This will obtain the file_disk from the thumb.
     * 
     */
    public function setFileDisk()
    {
        $this->file_disk = $this->thumb->fileDisk();
    }

    /**
     * This will obtain the channel_id from the thumb.
     * 
     */
    public function setChannelId()
    {
        $this->channel_id = $this->thumb->channelId();
    }

    /**
     * This will obtain the filename of the thumb and set the filename property for the vignette.
     * 
     */
    public function setFileName()
    {
        list($fileName, $fileExtension) = explode('.', $this->thumb->fileName());
        $this->file_name = $fileName . self::_VIGNETTE_SUFFIX . '.' . $fileExtension;
    }

    /**
     * This function is checking if thumb file exists.
     * 
     */
    protected function thumbExists()
    {
        return Storage::disk($this->thumb->file_disk)->exists($this->thumb->relativePath);
    }

    /**
     * This function will create the vignette from the thumb.
     */
    public function make()
    {
        /** Verifying thumb file exists */
        if (!$this->thumbExists()) {
            throw new VignetteCreationFromMissingThumbException(
                "Thumb file { " . $this->thumb->relativePath . " } on disk {{ $this->thumb->file_disk }} for channel {{$this->channel_id}} is missing."
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
