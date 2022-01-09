<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\FileUploadFailureException;
use App\Jobs\SendFileBySFTP;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SendFileBySFTPTest extends TestCase
{
    use WithFaker;

    protected $destFolder;

    public function setUp(): void
    {
        parent::setUp();
        $this->destFolder = 'tests/' . $this->faker->word();
    }

    public function tearDown(): void
    {
        Storage::disk('remote')->deleteDirectory($this->destFolder);
        parent::tearDown();
    }

    /** @test */
    public function sending_file_should_succeed(): void
    {
        $localFile = __DIR__ . '/../Fixtures/images/sampleVig.jpg';
        $remoteFile = $this->destFolder . '/testVig.jpg';
        SendFileBySFTP::dispatchSync($localFile, $remoteFile, false);
        $this->assertTrue(Storage::disk('remote')->exists($remoteFile));
    }

    /** @test */
    public function sending_file_on_protected_folder_should_throw_exception(): void
    {
        $localFile = __DIR__ . '/../Fixtures/images/sampleVig.jpg';
        $remoteFile = 'protected/testVig.jpg';
        $this->expectException(FileUploadFailureException::class);
        $job = new SendFileBySFTP($localFile, $remoteFile, false);
        $job->handle();
    }
}
