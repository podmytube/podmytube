<?php

declare(strict_types=1);

namespace App\Events;

use App\Interfaces\InteractsWithMedia;
use App\Interfaces\InteractsWithPodcastable;
use App\Interfaces\Podcastable;
use App\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaUploadedByUser implements InteractsWithPodcastable, InteractsWithMedia
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /** @var \App\Media */
    public $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function podcastable(): Podcastable
    {
        return $this->media->channel;
    }

    public function media(): Media
    {
        return $this->media;
    }
}
