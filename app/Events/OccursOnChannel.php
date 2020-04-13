<?php

namespace App\Events;

use App\Channel;

/**
 * Simple abstract class for event that will receive a channel object.
 */
abstract class OccursOnChannel
{
    public $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public static function shouldUpdateChannel(Channel $channel)
    {
        return new static($channel);
    }
}
