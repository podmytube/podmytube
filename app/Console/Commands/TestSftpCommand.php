<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * @internal
 *
 * @coversNothing
 */
class TestSftpCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sftp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple test to check Storage Sftp connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->prologue();

        $filename = 'stylesheet.xsl';
        $localFilePath = base_path($filename);
        $this->info("About to transfer file from {$localFilePath} to REMOTE/tests", 'v');

        try {
            Storage::disk('remote')->putFileAs('tests', $localFilePath, $filename);
            $this->comment("File has been transfered from {$localFilePath} to REMOTE/tests", 'v');

            $errCode = 0;
        } catch (Throwable $thrown) {
            $message = 'Storage SFTP Transfer has failed with error ' . $thrown->getMessage();
            $this->error($message);
            Log::error($message);

            $errCode = 1;
        }
        $this->epilogue();

        return $errCode;
    }
}
