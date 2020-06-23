<?php

namespace App\Modules;

use App\Exceptions\UndefinedEnvironmentVariable;
use App\Media;

class EnclosureUrl
{
    public const MP3_URL_KEY = 'MP3_URL';

    protected $media;
    protected $mp3BaseUrl;
    protected $enclosureUrl;

    private function __construct(Media $media)
    {
        if (!getenv(self::MP3_URL_KEY)) {
            throw new UndefinedEnvironmentVariable(
                'Environment variable {MP3_URL_KEY} is not defined.'
            );
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
        $separator = '/';
        $this->enclosureUrl =
            getenv(self::MP3_URL_KEY) .
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
