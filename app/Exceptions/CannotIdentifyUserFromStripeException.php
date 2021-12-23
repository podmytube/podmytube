<?php

declare(strict_types=1);

namespace App\Exceptions;

class CannotIdentifyUserFromStripeException extends PodmytubeException
{
    protected $message = 'The informations received from stripe does not allow us to identify the customer.';
}
