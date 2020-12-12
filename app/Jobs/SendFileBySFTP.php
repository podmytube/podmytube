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

    public $cleanAfter = false;

    public function __construct(
        string $localFilePath,
        string $remoteFilePath,
        bool $cleanAfter = false
    ) {
        $this->localFilePath = $localFilePath;
        $this->remoteFilePath = $remoteFilePath;
        $this->cleanAfter = $cleanAfter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $destFolder = pathinfo($this->remoteFilePath, PATHINFO_DIRNAME);
        $destFilename = pathinfo($this->remoteFilePath, PATHINFO_BASENAME);
        Log::debug(
            'About to copy file on ' . self::REMOTE_DISK,
            [
                'localFilePath' => $this->localFilePath,
                'remoteFilePath' => $this->remoteFilePath,
                'destFolder' => $destFolder,
                'destFilename' => $destFilename,
            ]
        );

        $result = Storage::disk(self::REMOTE_DISK)->putFileAs(
            $destFolder,
            $this->localFilePath,
            $destFilename
        );

        if ($result === false) {
            throw new FileUploadFailureException(
                "Uploading file from {$this->localFilePath} to {$this->remoteFilePath} has failed"
            );
        }
        Log::debug("file {$destFilename} has been uploaded");

        /** granting +x perms to folder */
        $result = Storage::disk(self::REMOTE_DISK)->setVisibility(
            $destFolder,
            'public'
        );
        Log::debug("folder {$destFolder} is visible");

        if ($result === false) {
            throw new FileUploadFailureException(
                "Setting visibility for {$destFolder} has failed"
            );
        }

        if ($this->cleanAfter === true) {
            unlink($this->localFilePath);
        }

        return true;
    }
}