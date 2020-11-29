<?php

namespace Tests\Unit;

use App\Channel;
use App\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UnderstandingStorageTest extends TestCase
{
    use RefreshDatabase;

    /** \App\Channel $channel channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function tearDown(): void
    {
        if (Storage::disk('tmp')->exists(Thumb::DEFAULT_THUMB_FILE)) {
            Storage::disk('tmp')->delete(Thumb::DEFAULT_THUMB_FILE);
        }
        parent::tearDown();
    }

    public function testFileExists()
    {
        $this->assertTrue(Storage::disk(Thumb::LOCAL_STORAGE_DISK)->exists(Thumb::DEFAULT_THUMB_FILE));
        $this->assertFalse(Storage::disk('tmp')->exists(Thumb::DEFAULT_THUMB_FILE));
    }

    public function testPath()
    {
        $rootPath = Storage::disk(Thumb::LOCAL_STORAGE_DISK)->path('');
        $this->assertEquals(
            $rootPath . Thumb::DEFAULT_THUMB_FILE,
            Storage::disk(Thumb::LOCAL_STORAGE_DISK)->path(Thumb::DEFAULT_THUMB_FILE)
        );
    }

    public function testPutFileAs()
    {
        /**
         * putFileAs('destination folder', $fileHandler, 'destination file name')
         */
        Storage::disk('tmp')->putFileAs(
            '', // mean I will copy the file to the root of disk tmp
            Storage::disk(Thumb::LOCAL_STORAGE_DISK)->path(Thumb::DEFAULT_THUMB_FILE),
            Thumb::DEFAULT_THUMB_FILE
        );

        $this->assertTrue(Storage::disk('tmp')->exists(Thumb::DEFAULT_THUMB_FILE));
    }
}
