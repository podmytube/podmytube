<?php

declare(strict_types=1);

namespace App\Exceptions;

class AssembleOutputFileIsNotWritableException extends PodmytubeException
{
    protected $message = 'Output file is not writable.';
}
