<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CheckSftpCommandTest extends TestCase
{
    /** @test */
    public function check_sftp_should_succeed(): void
    {
        Storage::fake('remote');
        $this->artisan('check:sftp')->assertExitCode(0);
    }
}
