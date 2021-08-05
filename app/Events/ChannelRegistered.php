<?php

declare(strict_types=1);

namespace App\Events;

use App\Interfaces\InteractsWithPodcastable;
use App\Interfaces\Podcastable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelRegistered implements InteractsWithPodcastable
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /** @var \App\Interfaces\Podcastable */
    public $podcastable;

    public function __construct(Podcastable $podcastable)
    {
        $this->podcastable = $podcastable;
    }

    public function podcastable(): Podcastable
    {
        return $this->podcastable;
    }
}
