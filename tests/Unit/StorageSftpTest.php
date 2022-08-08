<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorageSftpTest extends TestCase
{
    /** @var array $filesToDelete */
    protected $filesToDelete = [];

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('remote');
    }

    public function tearDown(): void
    {
        if (!count($this->filesToDelete)) {
            return;
        }
        Storage::disk('tmp')->delete($this->filesToDelete);
        Storage::disk('remote')->delete($this->filesToDelete);
    }

    /** @test */
    public function pushing_many_files_with_one_connection()
    {
        $nbFilesToCreate = 10;
        $filesCreated = $this->createRandomFilesIn($nbFilesToCreate, 'tmp', 'mp3');
        $this->addFilesToBeDeletedLocallyOnceFinished($filesCreated);

        array_map(function ($fileName) {
            $remotePath = "tests/{$fileName}";
            Storage::disk('remote')->put(
                $remotePath,
                Storage::disk('tmp')->get($fileName)
            );
            $this->assertTrue(Storage::disk('remote')->exists($remotePath));
        }, $filesCreated);
    }

    /**
     * ===================================================================
     * Helpers
     * ===================================================================
     */
    public function addFilesToBeDeletedLocallyOnceFinished(array $filesTodelete)
    {
        $this->filesToDelete = array_merge($this->filesToDelete, $filesTodelete);
    }

    public function createRandomFilesIn($nbFilesToCreate = 1, string $diskName = 'tmp', string $fileExtension = 'mp3'): array
    {
        $filesCreated = [];
        for ($i = 0; $i < $nbFilesToCreate; $i++) {
            $filesCreated[] = $this->createRandomFileIn($diskName, $fileExtension);
        }
        return $filesCreated;
    }

    public function createRandomFileIn(string $diskName = 'tmp', string $fileExtension = 'mp3')
    {
        /** generating fake filename with fileExtension asked */
        $fileName = Str::random(4) . ".{$fileExtension}";
        Storage::disk($diskName)->put($fileName, '');
        return $fileName;
    }
}
