<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\FileUploadFailureException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SendFileBySFTP implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const REMOTE_DISK = 'remote';

    /** @var string */
    public $localFilePath;

    /** @var string */
    public $remoteFilePath;

    /** @var bool clean local file if true */
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
     */
    public function handle()
    {
        $destFolder = pathinfo($this->remoteFilePath, PATHINFO_DIRNAME);
        $destFilename = pathinfo($this->remoteFilePath, PATHINFO_BASENAME);

        try {
            /*
            |--------------------------------------------------------------------------
            | IMPORTANT
            |--------------------------------------------------------------------------
            | when creating both folder & file with putFileAs, folder is
            | sometimes created with perms like `drwx------`.
            | adding makeDirectory seems to use config dir perms or at least
            | is creating folder with perms drwxr-xr-x which is better
            */
            Storage::disk(self::REMOTE_DISK)->makeDirectory($destFolder);
            Storage::disk(self::REMOTE_DISK)->putFileAs($destFolder, $this->localFilePath, $destFilename);
        } catch (Throwable $thrown) {
            $exception = new FileUploadFailureException();

            $message = 'date : ' . now()->toDateTimeString() . PHP_EOL;
            $message .= 'user : ' . config('filesystems.disks.' . self::REMOTE_DISK . '.username') . PHP_EOL;
            $message .= 'host : ' . config('filesystems.disks.' . self::REMOTE_DISK . '.host') . PHP_EOL;
            $message .= "localFilePath : {$this->localFilePath}" . PHP_EOL;
            $message .= "remoteFilePath : {$this->remoteFilePath}" . PHP_EOL;
            $message .= "destFolder : {$destFolder}" . PHP_EOL;
            $message .= "destFilename : {$destFilename}" . PHP_EOL;
            $message .= 'error was ' . $thrown->getMessage() . PHP_EOL;
            $exception->addInformations($message);
            Log::debug($exception->getMessage());

            throw $exception;
        }

        if ($this->cleanAfter === true) {
            // Log::notice("Cleaning {$this->localFilePath}.");
            unlink($this->localFilePath);
        }

        return 0;
    }
}
