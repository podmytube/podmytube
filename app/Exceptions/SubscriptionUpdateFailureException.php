<?php

declare(strict_types=1);

namespace App\Exceptions;

class SubscriptionUpdateFailureException extends PodmytubeException
{
    protected $message = 'Updating subscription has failed.';
}
