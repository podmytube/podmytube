<?php

namespace App\Jobs;

use App\Exceptions\FileUploadFailureException;
use App\Modules\NiceSSH;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFileBySFTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const REMOTE_DISK = 'remote';

    protected string $host;
    protected string $user;
    protected string $privateKeyPath;
    protected string $rootPath;

    public string $localFilePath;
    public string $remoteFilePath;

    /** @var bool $cleanAfter clean local file if true */
    public $cleanAfter = false;

    public function __construct(
        string $localFilePath,
        string $remoteFilePath,
        bool $cleanAfter = false
    ) {
        $this->localFilePath = $localFilePath;
        $this->remoteFilePath = $remoteFilePath;
        $this->cleanAfter = $cleanAfter;

        $this->host = config('filesystems.disks.' . self::REMOTE_DISK . '.host');
        $this->user = config('filesystems.disks.' . self::REMOTE_DISK . '.username');
        $this->privateKeyPath = config('filesystems.disks.' . self::REMOTE_DISK . '.privateKey');
        $this->rootPath = config('filesystems.disks.' . self::REMOTE_DISK . '.root');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!NiceSSH::init($this->host, $this->user, $this->privateKeyPath, $this->rootPath)->putFile($this->localFilePath, $this->remoteFilePath)) {
            throw new FileUploadFailureException("Uploading file {$this->localFilePath} to {$this->remoteFilePath} has failed.");
        }

        if ($this->cleanAfter === true) {
            Log::debug("Cleaning {$this->localFilePath}.");
            unlink($this->localFilePath);
        }

        return true;
    }
}
