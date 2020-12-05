<?php

namespace App\Events;

use App\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThumbUpdated extends OccursOnChannel
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Channel $channel */
    public $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }
}
