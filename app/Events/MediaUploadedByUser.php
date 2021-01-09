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

    /** @var \App\Channel $channel */
    public $channel;

    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->channel = $media->channel;
    }
}
