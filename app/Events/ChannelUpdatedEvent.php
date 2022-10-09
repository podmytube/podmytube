<?php

declare(strict_types=1);

namespace App\Events;

use App\Interfaces\InteractsWithPodcastable;
use App\Interfaces\Podcastable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelUpdatedEvent implements InteractsWithPodcastable
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Podcastable $podcastable)
    {
    }

    public function podcastable(): Podcastable
    {
        return $this->podcastable;
    }
}
