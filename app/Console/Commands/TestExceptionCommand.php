<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\DoNotReportToSentryException;
use Illuminate\Console\Command;
use InvalidArgumentException;

class TestExceptionCommand extends Command
{
    /** @var string */
    protected $signature = 'test:exception';

    /** @var string */
    protected $description = 'Will send an exception to check if log/email is running fine.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        report(new InvalidArgumentException("Don't panic !!! This is a test exception."));
        report(new DoNotReportToSentryException('This exception should not be sent to sentry.'));
    }
}
