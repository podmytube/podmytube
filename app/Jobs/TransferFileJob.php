<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\TransferFileDestinationFolderFailureException;
use App\Exceptions\TransferFileFailureException;
use App\Exceptions\TransferFileSourceFileDoNoExistException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TransferFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // The number of times the job may be attempted.
    public int $tries = 3;

    // The number of seconds to wait before retrying the job.
    public int $backoff = 10;

    public function __construct(
        protected string $sourceDisk,
        protected string $sourceFilePath,
        protected string $destinationDisk,
        protected string $destinationFilePath,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // checking source file really exists
        throw_unless(
            Storage::disk($this->sourceDisk)->exists($this->sourceFilePath),
            new TransferFileSourceFileDoNoExistException("File {$this->sourceDisk}:{$this->sourceFilePath} does not exists.")
        );

        // creating destination folder
        $destinationFolder = dirname($this->destinationFilePath);
        throw_unless(
            Storage::disk($this->destinationDisk)->makeDirectory($destinationFolder),
            new TransferFileDestinationFolderFailureException("Creating folder {$this->destinationDisk}:{$destinationFolder} has failed.")
        );

        // copying file
        throw_unless(
            Storage::disk($this->destinationDisk)->put(
                $this->destinationFilePath,
                Storage::disk($this->sourceDisk)->get($this->sourceFilePath),
            ),
            new TransferFileFailureException(
                "Transferring file from {$this->sourceDisk}:{$this->sourceFilePath} \\
                to {$this->destinationDisk}:{$destinationFolder} has failed."
            )
        );
    }
}
