<?php

declare(strict_types=1);

namespace App\Exceptions;

class YoutubeGenericErrorException extends PodmytubeException
{
    protected $message = 'Youtube send us a response with an errorcode.';
}
