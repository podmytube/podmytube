<?php

declare(strict_types=1);

namespace App\Exceptions;

class EmptySubscriptionReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'There is no subscription_id in json received from stripe.';
}
