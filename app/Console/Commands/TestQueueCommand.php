<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\TestJob;
use Illuminate\Console\Command;

class TestQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'will dispatch a local copy file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        TestJob::dispatch();
    }
}
