<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\FileUploadFailureException;
use App\Jobs\SendFileByRsync;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SendFileByRsyncTest extends TestCase
{
    use WithFaker;

    protected string $destFolder;
    protected string $sourceFile;
    protected string $filename;

    public function setUp(): void
    {
        parent::setUp();

        $this->destFolder = 'tests/' . $this->faker->word();
        $fixtureFile = fixtures_path('images/sampleVig.jpg');
        $this->filename = $this->faker->word() . '.jpg';
        $this->sourceFile = '/tmp/' . $this->filename;
        $this->remoteFile = $this->destFolder . '/' . $this->filename;
        file_put_contents($this->sourceFile, file_get_contents($fixtureFile));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function not_existing_source_file_should_throw_exception(): void
    {
        $this->expectException(FileUploadFailureException::class);
        $job = new SendFileByRsync('/this/file/do/not/exists', $this->remoteFile, false);
        $job->handle();
    }

     /** @test */
     public function not_readable_source_file_should_throw_exception(): void
     {
         $this->expectException(FileUploadFailureException::class);
         $job = new SendFileByRsync('/etc/shadow', $this->remoteFile, false);
         $job->handle();
     }

    /** @test */
    public function sending_file_should_succeed_and_local_should_be_still_present(): void
    {
        $job = new SendFileByRsync($this->sourceFile, $this->remoteFile, false);
        $result = $job->handle();
        $this->assertTrue($result);
        $this->assertFileExists($this->sourceFile);
    }

    /** @test */
    public function sending_file_then_clean_local_should_succeed(): void
    {
        $job = new SendFileByRsync($this->sourceFile, $this->remoteFile, true);
        $result = $job->handle();
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($this->sourceFile);
    }

    /** @test */
    public function sending_file_over_existing_file_should_work(): void
    {
        $job = new SendFileByRsync($this->sourceFile, $this->remoteFile);
        $job->handle();

        // sending it again
        $job = new SendFileByRsync($this->sourceFile, $this->remoteFile);
        $result = $job->handle();

        $this->assertTrue($result);
    }
}
