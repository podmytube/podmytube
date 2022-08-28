<?php

declare(strict_types=1);

namespace App\Exceptions;

class NotReadableFileException extends PodmytubeException
{
    protected $message = 'File is not readable.';
}
