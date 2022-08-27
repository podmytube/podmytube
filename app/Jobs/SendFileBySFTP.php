<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\FileUploadFailureException;
use App\Exceptions\FileUploadUnreadableFileException;
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
        Log::info(__CLASS__ . '::' . __FUNCTION__ . " Moving File {$this->localFilePath} to {$this->remoteFilePath}");

        try {
            throw_unless(
                file_exists($this->localFilePath),
                new FileUploadUnreadableFileException("File {$this->localFilePath} do not exists.")
            );

            throw_unless(
                is_readable($this->localFilePath),
                new FileUploadUnreadableFileException("File {$this->localFilePath} is not readable.")
            );

            $destFolder = pathinfo($this->remoteFilePath, PATHINFO_DIRNAME);
            $destFilename = pathinfo($this->remoteFilePath, PATHINFO_BASENAME);

            $content = file_get_contents($this->localFilePath);
            throw_if(
                $content === false,
                new FileUploadUnreadableFileException("Cannot get content of file {$this->localFilePath}.")
            );

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
            Storage::disk(self::REMOTE_DISK)->put($this->remoteFilePath, $content);
        } catch (Throwable $thrown) {
            $exception = new FileUploadFailureException();

            $message = 'date : ' . now()->toDateTimeString() . PHP_EOL;
            $message .= 'user : ' . config('filesystems.disks.' . self::REMOTE_DISK . '.username') . PHP_EOL;
            $message .= 'host : ' . config('filesystems.disks.' . self::REMOTE_DISK . '.host') . PHP_EOL;
            $message .= "localFilePath : {$this->localFilePath}" . PHP_EOL;
            $message .= "remoteFilePath : {$this->remoteFilePath}" . PHP_EOL;
            $message .= "destFolder : {$destFolder}" . PHP_EOL;
            $message .= "destFilename : {$destFilename}" . PHP_EOL;
            $message .= 'as user : ' . get_current_user() . PHP_EOL;
            $message .= 'error was ' . $thrown->getMessage() . PHP_EOL;
            $exception->addInformations($message);
            Log::alert($exception->getMessage());

            throw $exception;
        }

        if ($this->cleanAfter === true) {
            // Log::notice("Cleaning {$this->localFilePath}.");
            unlink($this->localFilePath);
        }
        Log::info("File {$this->localFilePath} has been moved to {$this->remoteFilePath}");

        return 0;
    }
}
