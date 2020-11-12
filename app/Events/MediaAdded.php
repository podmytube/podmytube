<?php

namespace App\Events;

use App\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaAdded extends OccursOnChannel
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** \App\Media */
    public $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }
}
