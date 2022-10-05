<?php

declare(strict_types=1);

namespace App\Exceptions;

class DoNotReportToSentryException extends PodmytubeException
{
    protected $message = 'File transfer has failed.';
}
