<?php

declare(strict_types=1);

namespace App\Exceptions;

class AssembleOutputFileMissingException extends PodmytubeException
{
    protected $message = 'Output file is missing.';
}
