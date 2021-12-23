<?php

declare(strict_types=1);

namespace App\Exceptions;

class EmptyCustomerReceivedFromStripeException extends PodmytubeException
{
    protected $message = 'There is no customer information in json received from stripe.';
}
