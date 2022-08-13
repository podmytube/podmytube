<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class CheckSftp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:sftp';

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
        $filename = 'stylesheet.xsl';
        $localFilePath = base_path($filename);
        $this->info("About to transfer file from {$localFilePath} to REMOTE/tests", 'v');

        try {
            Storage::disk('remote')->putFileAs('tests', $localFilePath, $filename);
            $this->comment("File has been transfered from {$localFilePath} to REMOTE/tests", 'v');

            return 0;
        } catch (Throwable $thrown) {
            $this->error('Transfer has failed with error ' . $thrown->getMessage());

            return 1;
        }
    }
}
