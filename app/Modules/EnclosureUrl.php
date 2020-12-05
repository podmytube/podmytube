<?php

namespace App\Modules;

use App\Media;

class EnclosureUrl
{
    protected $media;
    protected $mp3BaseUrl;
    protected $enclosureUrl;

    private function __construct(Media $media)
    {
        $this->media = $media;
        $this->setEnclosureUrl();
    }

    public static function create(...$params): EnclosureUrl
    {
        return new static(...$params);
    }

    protected function setEnclosureUrl()
    {
        $separator = '/';
        $this->enclosureUrl = config('app.mp3_url') .
            $separator .
            $this->media->channel_id .
            $separator .
            $this->media->media_id .
            '.mp3';
    }

    public function get()
    {
        return $this->enclosureUrl;
    }
}
