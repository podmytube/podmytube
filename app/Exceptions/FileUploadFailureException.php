<?php

declare(strict_types=1);

namespace App\Exceptions;

class FileUploadFailureException extends PodmytubeException
{
    protected $message = 'File transfer has failed.';
}
