<?php

namespace App\Jobs;

use App\Exceptions\FileUploadFailureException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendFileBySFTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const REMOTE_DISK = 'kim1';

    /** @var string $localFilePath */
    public $localFilePath;

    /** @var string $remoteFilePath */
    public $remoteFilePath;

    public function __construct(
        string $localFilePath,
        string $remoteFilePath
    ) {
        $this->localFilePath = $localFilePath;
        $this->remoteFilePath = $remoteFilePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::notice("About to copy {$this->localFilePath} to {$this->remoteFilePath} on " . self::REMOTE_DISK . '.');
        $result = Storage::disk(self::REMOTE_DISK)->putFileAs(
            pathinfo($this->remoteFilePath, PATHINFO_DIRNAME),
            $this->localFilePath,
            pathinfo($this->remoteFilePath, PATHINFO_BASENAME)
        );

        if ($result === false) {
            throw new FileUploadFailureException("Uploading file from {$this->localFilePath} to {$this->remoteFilePath} as failed");
        }

        /** granting +x perms to folder */
        Storage::disk(self::REMOTE_DISK)->setVisibility(
            pathinfo($this->remoteFilePath, PATHINFO_DIRNAME),
            'public'
        );

        return true;
    }
}
