<?php

namespace App\Events;

use App\Channel as Channel;

/**
 * Simple abstract class for event that will receive a channel object.
 */
abstract class ChannelIsConcerned
{
    public $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }
}