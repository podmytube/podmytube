<?php

namespace App\Events;

use App\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Channel $channel */
    protected $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }
}
