<?php

declare(strict_types=1);

namespace App\Console\Commands\Traits;

use Symfony\Component\Console\Helper\ProgressBar;

trait WithProgressBar
{
    protected ProgressBar $bar;

    protected function initProgressBar(int $nbItems)
    {
        if (!$this->getOutput()->isVerbose()) {
            return false;
        }

        $this->bar = $this->output->createProgressBar($nbItems);
        $this->bar->start();

        return true;
    }

    protected function finishProgressBar()
    {
        if (!$this->getOutput()->isVerbose()) {
            return false;
        }
        $this->bar->finish();
    }

    protected function makeProgressBarProgress()
    {
        if (!$this->getOutput()->isVerbose()) {
            return false;
        }
        $this->bar->advance();
    }
}
