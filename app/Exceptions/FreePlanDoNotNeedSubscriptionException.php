<?php

namespace App\Exceptions;

use App\Channel;

use Exception;

class FreePlanDoNotNeedSubscriptionException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(Channel $channel)
    {
        \Log::debug("Channel {{$channel->channel_id}} has a free plan and does not need subscription.");
    }
}
