<?php

declare(strict_types=1);

namespace App\Exceptions;

class EmptyChannelIdReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'There is no channel_id in json received from stripe.';
}
