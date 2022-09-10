<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\AssembleCommandHasFailedException;
use App\Exceptions\AssembleOutputFileIsNotWritableException;
use App\Exceptions\AssembleOutputFileMissingException;
use ArgumentCountError;
use Illuminate\Support\Arr;

class AssembleAudioFilesFactory
{
    protected bool $verbose;
    protected ?string $outputFile = null;

    private function __construct(...$files)
    {
        $this->files = Arr::wrap($files);
        throw_if(count($this->files) < 2, new ArgumentCountError('2 files are required to be assemble'));
    }

    public static function files(...$files): static
    {
        return new static(...$files);
    }

    public function outputFile(string $outputFile): static
    {
        throw_unless(touch($outputFile), new AssembleOutputFileIsNotWritableException("Output file {$outputFile} is not writable."));

        throw_unless(is_writable($outputFile), new AssembleOutputFileIsNotWritableException("Output file {$outputFile} is not writable."));

        $this->outputFile = $outputFile;

        return $this;
    }

    public function command(): string
    {
        $filesToAssemble = implode('|', $this->files);

        throw_if($this->outputFile === null, new AssembleOutputFileMissingException());

        /*
         * -v 8 => quiet
         * - y => overwrite
         * -i => specify the input
         */
        return "ffmpeg -v 8 -y -i 'concat:{$filesToAssemble}' -c:a libmp3lame " . $this->outputFile;
    }

    public function assemble(): bool
    {
        passthru($this->command(), $err);

        throw_unless(
            $err === 0,
            new AssembleCommandHasFailedException("Assembling command {$this->command()} has failed.")
        );

        return true;
    }
}
