<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\FileUploadNotExistingFileException;
use App\Exceptions\FileUploadUnreadableFileException;
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
        Storage::fake('remote');

        $this->destFolder = 'tests/' . $this->faker->word();
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
    public function not_existing_source_file_should_throw_exception(): void
    {
        $this->expectException(FileUploadNotExistingFileException::class);
        $job = new SendFileBySFTP('/this/file/do/not/exists', $this->remoteFile, false);
        $job->handle();
    }

     /** @test */
     public function not_readable_source_file_should_throw_exception(): void
     {
         $this->expectException(FileUploadUnreadableFileException::class);
         $job = new SendFileBySFTP('/etc/shadow', $this->remoteFile, false);
         $job->handle();
     }

    /** @test */
    public function sending_file_should_succeed_and_local_should_be_still_present(): void
    {
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile, false);
        $result = $job->handle();
        $this->assertTrue($result);
        $this->assertTrue(Storage::disk('remote')->exists($this->remoteFile));
        $this->assertFileExists($this->sourceFile);
    }

    /** @test */
    public function sending_file_then_clean_local_should_succeed(): void
    {
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile, true);
        $result = $job->handle();
        $this->assertTrue($result);
        $this->assertTrue(Storage::disk('remote')->exists($this->remoteFile));
        $this->assertFileDoesNotExist($this->sourceFile);
    }

    /** @test */
    public function sending_file_over_existing_file_should_work(): void
    {
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile);
        $job->handle();

        // sending it again
        $job = new SendFileBySFTP($this->sourceFile, $this->remoteFile);
        $result = $job->handle();

        $this->assertTrue($result);
    }
}
