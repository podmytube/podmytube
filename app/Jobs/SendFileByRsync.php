<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\FileUploadFailureException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendFileByRsync implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $localFilePath,
        public string $remoteFilePath,
        public bool $cleanAfter = false
    ) {
    }

    public function handle(): bool
    {
        Log::info(__CLASS__ . '::' . __FUNCTION__ . " Rsync File {$this->localFilePath} to {$this->remoteFilePath} on " . config('app.podhost_ssh_host'));
        $destFolder = pathinfo($this->remoteFilePath, PATHINFO_DIRNAME);
        $destFilename = pathinfo($this->remoteFilePath, PATHINFO_BASENAME);
        $command = 'not defined yet';

        try {
            throw_unless(
                file_exists($this->localFilePath),
                new Exception("File {$this->localFilePath} do not exists.")
            );

            throw_unless(
                is_readable($this->localFilePath),
                new Exception("File {$this->localFilePath} is not readable.")
            );

            $parentFolder = config('app.podhost_ssh_root') . '/' . $destFolder;
            $userAndHost = config('app.podhost_ssh_user') . '@' . config('app.podhost_ssh_host');
            $absoluteRemoteFilePath = config('app.podhost_ssh_root') . '/' . $this->remoteFilePath;
            $sshOptions = "-e 'ssh -i .ssh/kimUpload -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null'";
            $rsyncPath = '--rsync-path="mkdir -p ' . $parentFolder . ' && rsync"';

            $command = "rsync -avz --quiet {$rsyncPath} {$sshOptions} {$this->localFilePath} {$userAndHost}:{$absoluteRemoteFilePath}";
            $result = exec($command);
            throw_if(
                $result === false,
                new Exception('Uploading file from ' . $this->localFilePath . ' to ' . $this->remoteFilePath . ' has failed.')
            );
        } catch (Throwable $thrown) {
            $exception = new FileUploadFailureException();
            $message = 'date : ' . now()->toDateTimeString() . PHP_EOL;
            $message .= 'user : ' . config('app.podhost_ssh_user') . PHP_EOL;
            $message .= 'host : ' . config('app.podhost_ssh_host') . PHP_EOL;
            $message .= "localFilePath : {$this->localFilePath}" . PHP_EOL;
            $message .= "remoteFilePath : {$this->remoteFilePath}" . PHP_EOL;
            $message .= "destFolder : {$destFolder}" . PHP_EOL;
            $message .= "destFilename : {$destFilename}" . PHP_EOL;
            $message .= 'as user : ' . get_current_user() . PHP_EOL;
            $message .= "command : {$command} " . PHP_EOL;
            $message .= 'error was ' . $thrown->getMessage() . PHP_EOL;
            $exception->addInformations($message);
            Log::alert($message);

            throw $exception;
        }

        if ($this->cleanAfter === true) {
            // Log::notice("Cleaning {$this->localFilePath}.");
            unlink($this->localFilePath);
        }
        Log::info("File {$this->localFilePath} has been moved to {$this->remoteFilePath} on " . config('app.podhost_ssh_host'));

        return true;
    }
}
