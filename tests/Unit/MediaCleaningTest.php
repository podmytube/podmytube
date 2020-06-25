<?php

namespace Tests\Unit;

use App\Jobs\MediaCleaning as MediaCleaning;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaCleaningTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake(Media::DISK);
    }

    public function testMediaCleaningIsWorkingFine()
    {
        $media = factory(Media::class)->create();

        Storage::put(
            $media->relativePath(),
            file_get_contents(base_path('tests/fixtures/Audio/l8i4O7_btaA.mp3'))
        );

        $this->assertTrue(Storage::exists($media->relativePath()));
        MediaCleaning::dispatchNow($media);
        $this->assertFalse(Storage::exists($media->relativePath()));
        $this->assertTrue($media->trashed());
    }
}
