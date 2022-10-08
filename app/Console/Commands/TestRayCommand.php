<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use Illuminate\Console\Command;

/**
 * @internal
 *
 * @coversNothing
 */
class TestRayCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ray';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple test to view ray display in ray window';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->prologue();
        ray('hello Ray')->green();

        $this->epilogue();

        return 0;
    }
}
