<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Storage;

/**
 * @internal
 *
 * @coversNothing
 */
class TestSftpCommandTest extends CommandTestCase
{
    /** @test */
    public function check_sftp_should_succeed(): void
    {
        Storage::fake('remote');
        $this->artisan('test:sftp')->assertExitCode(0);
    }
}
