<?php

declare(strict_types=1);

namespace App\Exceptions;

class StripeSessionCreationFailureException extends PodmytubeException
{
    protected $message = 'Stripe session creation for checkout has failed.';
}
