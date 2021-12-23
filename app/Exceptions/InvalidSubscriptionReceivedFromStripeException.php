<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidSubscriptionReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'Subscription_id received from stripe seems invalid.';
}
