<?php

declare(strict_types=1);

namespace App\Exceptions;

class EmptyPlanReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'Plan id received from stripe is empty.';
}
