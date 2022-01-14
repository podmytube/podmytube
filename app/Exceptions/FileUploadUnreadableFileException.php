<?php

declare(strict_types=1);

namespace App\Exceptions;

class FileUploadUnreadableFileException extends PodmytubeException
{
    protected $message = 'File to be uploaded is not readable.';
}
