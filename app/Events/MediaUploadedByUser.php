<?php

namespace App\Events;

use App\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaUploadedByUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Media $media */
    public $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }
}
