<?php

namespace App\Modules;

use App\Media;
use App\Exceptions\UndefinedEnvironmentVariable;

class EnclosureUrl
{
    protected $media;
    protected $mp3BaseUrl;
    protected $enclosureUrl;

    private function __construct(Media $media)
    {
        if(!getenv('MP3_URL')){
            throw new UndefinedEnvironmentVariable("Environment variable {".getenv('MP3_URL')."} is not defined.");
        }
        $this->media = $media;
        $this->setEnclosureUrl();
    }

    public static function create(...$params): EnclosureUrl
    {
        return new static(...$params);
    }

    protected function setEnclosureUrl()
    {
        $this->enclosureUrl = getenv('MP3_URL') . '/' . $this->media->channel_id . '/' . $this->media->media_id . '.mp3';
    }

    public function get()
    { 
        return $this->enclosureUrl;
    }
}
