<?php

declare(strict_types=1);

namespace App\Events;

use App\Interfaces\InteractsWithMedia;
use App\Interfaces\InteractsWithPodcastable;
use App\Interfaces\Podcastable;
use App\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaUploadedByUser implements InteractsWithPodcastable, InteractsWithMedia
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Media $media)
    {
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
