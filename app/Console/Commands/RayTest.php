<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * @internal
 * @coversNothing
 */
class RayTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ray:test';

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
        ray('hello Ray')->green();

        return 0;
    }
}
