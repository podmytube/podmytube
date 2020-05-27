<?php

namespace App\Events;

use App\Media;
use Illuminate\Queue\SerializesModels;

class MediaRegistered
{
    use SerializesModels;

    protected $media;

    /**
     * Create a new event instance.
     *
     * @param  \App\Media  $media
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * @return \App\Media the media that has been registered
     */
    public function getMedia()
    {
        return $this->media;
    }
}
