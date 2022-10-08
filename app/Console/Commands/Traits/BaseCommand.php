<?php

declare(strict_types=1);

namespace App\Console\Commands\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

trait BaseCommand
{
    protected string $typedCommand;
    protected string $logPrefix;
    protected array $logPrefixes = ['>>>', '###', '~~~', '&&&', '---', '@@@'];

    protected function isVerbose(): bool
    {
        return $this->getOutput()->isVerbose();
    }

    protected function chooseLogPrefix(): void
    {
        $this->logPrefix = Arr::random($this->logPrefixes);
    }

    protected function prologue(): void
    {
        $this->chooseLogPrefix();
        $this->typedCommand = $this->argument('command');

        if ($this->hasMoreArgumentsThanJustCommand()) {
            array_map(
                function (string $argumentKey): void {
                    $this->typedCommand .= ' ' . $this->argument($argumentKey);
                },
                $this->additionalArguments()
            );
        }

        if ($this->hasMoreOptionsThanDefaultOnes()) {
            array_map(
                function (string $optionKey): void {
                    if ($this->option($optionKey) === null
                        || $this->option($optionKey) === false
                    ) {
                        return;
                    }

                    $this->typedCommand .= ' --' . $optionKey;
                    if (
                        $this->option($optionKey) !== null
                        && !is_bool($this->option($optionKey))
                    ) {
                        $this->typedCommand .= '=' . $this->option($optionKey);
                    }
                },
                $this->additionalOptions()
            );
        }

        Log::info($this->logPrefix . " Command started : artisan {$this->typedCommand}");
    }

    protected function epilogue(): void
    {
        $ending = Carbon::createFromTimestampMs(microtime(true));
        $starting = Carbon::createFromTimestampMs(LARAVEL_START);
        Log::info($this->logPrefix . " Command artisan {$this->typedCommand} finished (" . $ending->diffForHumans($starting) . ')');
    }

    protected function additionalArguments(): array
    {
        return array_diff(array_keys($this->arguments()), ['command', '0']);
    }

    protected function hasMoreArgumentsThanJustCommand(): bool
    {
        return count($this->additionalArguments()) > 0;
    }

    protected function additionalOptions(): array
    {
        $defaultOptions = [
            'help',
            'quiet',
            'verbose',
            'version',
            'ansi',
            'no-interaction',
            'env',
        ];

        return array_diff(array_keys($this->options()), $defaultOptions);
    }

    protected function hasMoreOptionsThanDefaultOnes(): bool
    {
        return count($this->additionalOptions()) > 0;
    }
}
