<?php

namespace Tests\Unit;

use App\Events\ChannelUpdated;
use App\Jobs\MediaCleaning as MediaCleaning;
use App\Jobs\SendFileBySFTP;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaCleaningTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake(SendFileBySFTP::REMOTE_DISK);
        Event::fake();
    }

    public function testMediaCleaningIsWorkingFine()
    {
        /**
         * creating fake media with real file (storage disk is faked above)
         */
        $media = factory(Media::class)->create();
        Storage::put(
            $media->remoteFilePath(),
            file_get_contents(base_path('tests/fixtures/Audio/l8i4O7_btaA.mp3'))
        );
        /**
         * just to check media file exists
         */
        $this->assertTrue(Storage::exists($media->remoteFilePath()));

        /**
         * dispatching media deletion
         */
        MediaCleaning::dispatch($media);

        /**
         * checking all has been soft deleted
         */
        $this->assertFalse(Storage::exists($media->remoteFilePath()));
        $this->assertTrue($media->trashed());

        /**
         * all media informations should be null
         */
        $media->refresh();
        $this->assertNull($media->grabbed_at);
        $this->assertEquals(0, $media->length);
        $this->assertEquals(0, $media->duration);

        /**
         * an event should have been sent to rebuild podcast
         */
        Event::assertDispatched(ChannelUpdated::class);
    }
}
