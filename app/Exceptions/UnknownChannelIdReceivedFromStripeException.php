<?php

declare(strict_types=1);

namespace App\Exceptions;

class UnknownChannelIdReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'Channel_id received from stripe is unknown.';
}
