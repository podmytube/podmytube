<?php

namespace App\Events;

use App\Media;
use Illuminate\Queue\SerializesModels;

class MediaRegistered
{
    use SerializesModels;

    public $media;

    /**
     * Create a new event instance.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function __construct(Media $media)
    {
        dump('Media registered', $media, __FILE__ . '-' . __FUNCTION__);
        $this->media = $media;
    }
}
