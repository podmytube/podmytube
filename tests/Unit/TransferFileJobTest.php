<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\TransferFileSourceFileDoNoExistException;
use App\Jobs\TransferFileJob;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TransferFileJobTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected string $sourceFilePath;
    protected string $destinationFilePath;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('remote');
        $this->channel = Channel::factory()->create();
        $this->sourceFilePath = $this->channel->relativeFolderPath() . '/' . fake()->word() . '.mp3';
        $this->destinationFilePath = $this->sourceFilePath;
    }

    public function tearDown(): void
    {
        Storage::disk('remote')->deleteDirectory(dirname($this->sourceFilePath));
        Storage::disk('tmp')->deleteDirectory(dirname($this->destinationFilePath));
        parent::tearDown();
    }

    /** @test */
    public function transfer_file_from_unknown_disk_should_fail(): void
    {
        $job = new TransferFileJob(
            sourceDisk: 'unknown_from_disk',
            sourceFilePath: '',
            destinationDisk: 'tmp',
            destinationFilePath: '',
        );
        $this->expectException(InvalidArgumentException::class);
        $job->handle();
    }

    /** @test */
    public function transfer_file_from_unknown_file_should_fail(): void
    {
        $job = new TransferFileJob(
            sourceDisk: 'remote',
            sourceFilePath: 'this/files/is/unknown',
            destinationDisk: 'tmp',
            destinationFilePath: '',
        );
        $this->expectException(TransferFileSourceFileDoNoExistException::class);
        $job->handle();
    }

    /** @test */
    public function transfer_file_to_unknown_disk_should_fail(): void
    {
        $this->createFileOnDisk(
            'remote',
            $this->sourceFilePath,
            file_get_contents(fixtures_path('Audio/l8i4O7_btaA.mp3'))
        );
        $job = new TransferFileJob(
            sourceDisk: 'remote',
            sourceFilePath: $this->sourceFilePath,
            destinationDisk: 'unknown_destination_disk',
            destinationFilePath: '',
        );
        $this->expectException(InvalidArgumentException::class);
        $job->handle();
    }

    /** @test */
    public function transfer_file_from_remote_to_local_should_succeed(): void
    {
        $this->createFileOnDisk(
            'remote',
            $this->sourceFilePath,
            file_get_contents(fixtures_path('Audio/l8i4O7_btaA.mp3'))
        );

        $job = new TransferFileJob(
            sourceDisk: 'remote',
            sourceFilePath: $this->sourceFilePath,
            destinationDisk: 'tmp',
            destinationFilePath: $this->destinationFilePath,
        );
        $job->handle();

        $destinationFolder = dirname($this->destinationFilePath);
        $this->assertTrue(Storage::disk('tmp')->directoryExists($destinationFolder), "folder {$destinationFolder} should exist.");
        $this->assertTrue(Storage::disk('tmp')->exists($this->destinationFilePath), "file {$this->destinationFilePath} should exist.");
        $this->assertEquals(
            filesize(fixtures_path('Audio/l8i4O7_btaA.mp3')),
            Storage::disk('tmp')->size($this->destinationFilePath)
        );
    }

    /** @test */
    public function transfer_file_from_local_to_remote_should_succeed(): void
    {
        $this->createFileOnDisk(
            'tmp',
            $this->sourceFilePath,
            file_get_contents(fixtures_path('Audio/l8i4O7_btaA.mp3'))
        );

        $job = new TransferFileJob(
            sourceDisk: 'tmp',
            sourceFilePath: $this->sourceFilePath,
            destinationDisk: 'remote',
            destinationFilePath: $this->destinationFilePath,
        );
        $job->handle();

        $destinationFolder = dirname($this->destinationFilePath);
        $this->assertTrue(Storage::disk('remote')->directoryExists($destinationFolder), "folder {$destinationFolder} should exist.");
        $this->assertTrue(Storage::disk('remote')->exists($this->destinationFilePath), "file {$this->destinationFilePath} should exist.");
        $this->assertEquals(
            filesize(fixtures_path('Audio/l8i4O7_btaA.mp3')),
            Storage::disk('remote')->size($this->destinationFilePath)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */

    public function createFileOnDisk(string $diskName, string $filePath, string $fileContent): bool
    {
        Storage::disk($diskName)->makeDirectory(dirname($filePath));

        return Storage::disk($diskName)->put($filePath, $fileContent);
    }
}
