<?php

declare(strict_types=1);

namespace App\Exceptions;

class ChannelCreationHasFailedException extends PodmytubeException
{
    protected $message = 'Channel creation has failed.';
}
