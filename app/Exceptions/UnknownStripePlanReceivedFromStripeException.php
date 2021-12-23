<?php

declare(strict_types=1);

namespace App\Exceptions;

class UnknownStripePlanReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'Stripe plan id received from stripe is unknown.';
}
