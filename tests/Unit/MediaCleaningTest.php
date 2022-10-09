<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Events\ChannelUpdatedEvent;
use App\Jobs\MediaCleaning;
use App\Jobs\SendFileBySFTP;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MediaCleaningTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake(SendFileBySFTP::REMOTE_DISK);
        Event::fake();
    }

    public function test_media_cleaning_is_working_fine(): void
    {
        /**
         * creating fake media with real file (storage disk is faked above).
         */
        $media = Media::factory()->create();
        $preservedMediaId = $media->media_id;
        Storage::put(
            $media->remoteFilePath(),
            file_get_contents(base_path('tests/Fixtures/Audio/l8i4O7_btaA.mp3'))
        );

        // just to check media file exists
        $this->assertTrue(Storage::exists($media->remoteFilePath()));

        /**
         * dispatching media deletion.
         */
        $job = new MediaCleaning($media);
        $job->handle();

        // checking all has been soft deleted
        $this->assertFalse(Storage::exists($media->remoteFilePath()));
        $this->assertTrue($media->trashed());

        // all media informations should be null
        $mediaSoftDeleted = Media::withTrashed()
            ->where('media_id', '=', $preservedMediaId)
            ->first()
        ;

        $this->assertNull($mediaSoftDeleted->grabbed_at);
        $this->assertEquals(0, $mediaSoftDeleted->length);
        $this->assertEquals(0, $mediaSoftDeleted->duration);

        // an event should have been sent to rebuild podcast
        Event::assertDispatched(ChannelUpdatedEvent::class);
    }
}
