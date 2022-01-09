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

    protected string $destFolder;
    protected string $sourceFile;
    protected string $filename;

    public function setUp(): void
    {
        parent::setUp();
        $this->destFolder = 'tests/' . $this->faker->word();
        // I do not want to delete fixture file so I copy it.
        $fixtureFile = __DIR__ . '/../Fixtures/images/sampleVig.jpg';
        $this->filename = $this->faker->word() . '.jpg';
        $this->sourceFile = '/tmp/' . $this->filename;
        $this->remoteFile = $this->destFolder . '/' . $this->filename;
        file_put_contents($this->sourceFile, file_get_contents($fixtureFile));
    }

    public function tearDown(): void
    {
        Storage::disk('remote')->deleteDirectory($this->destFolder);
        parent::tearDown();
    }

    /** @test */
    public function sending_file_should_succeed_and_local_should_be_still_present(): void
    {
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile, false);
        $job->handle();
        $this->assertTrue(Storage::disk('remote')->exists($this->remoteFile));
        $this->assertFileExists($this->sourceFile);
    }

    /** @test */
    public function sending_file_then_clean_local_should_succeed(): void
    {
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile, true);
        $job->handle();
        $this->assertTrue(Storage::disk('remote')->exists($this->remoteFile));
        $this->assertFileDoesNotExist($this->sourceFile);
    }

    /** @test */
    public function sending_file_on_protected_folder_should_throw_exception(): void
    {
        $this->remoteFile = 'protected/' . $this->filename;
        $this->expectException(FileUploadFailureException::class);
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile, false);
        $job->handle();
        $this->assertFalse(Storage::disk('remote')->exists($this->remoteFile));
    }
}
