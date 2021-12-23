<?php

declare(strict_types=1);

namespace App\Exceptions;

class ChannelOwnerMismatchingStripeException extends PodmytubeException
{
    protected $message = 'User received from stripe is not the owner.';
}
