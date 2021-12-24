<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidCustomerEmailReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'Customer email received from stripe is not valid.';
}
