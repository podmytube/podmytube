<?php

namespace App\Events;

use App\Interfaces\Podcastable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PodcastUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Interfaces\Podcastable $podcastable */
    public $podcastable;

    public function __construct(Podcastable $podcastable)
    {
        $this->podcastable = $podcastable;
    }
}
