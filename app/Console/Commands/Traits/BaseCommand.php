<?php

declare(strict_types=1);

namespace App\Console\Commands\Traits;

trait BaseCommand
{
    protected function isVerbose(): bool
    {
        return $this->getOutput()->isVerbose();
    }
}
