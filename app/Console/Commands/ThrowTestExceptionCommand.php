<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;

class ThrowTestExceptionCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'throw:exception';

    /** @var string $description */
    protected $description = 'Will send an exception to check if log/email is running fine.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        throw new InvalidArgumentException("Don't panic !!! This is a test exception.");
    }
}
