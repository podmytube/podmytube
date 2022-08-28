<?php

declare(strict_types=1);

namespace App\Exceptions;

class FileUploadNotExistingFileException extends PodmytubeException
{
    protected $message = 'File to be uploaded do not exists.';
}
