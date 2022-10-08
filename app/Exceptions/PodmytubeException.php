<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class PodmytubeException extends Exception
{
    public function addInformations(string $moreInformations): void
    {
        $this->message .= PHP_EOL . $moreInformations;
    }
}
