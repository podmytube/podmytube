<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Jobs\MediaCleaning;
use App\Jobs\PodcastableCleaning;
use App\Jobs\SendFileBySFTP;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PodcastableCleaningTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Interfaces\Podcastable */
    protected $podcastableToDelete;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake(SendFileBySFTP::REMOTE_DISK);
    }

    /** @test */
    public function channel_cleaning_is_working_fine(): void
    {
        Bus::fake();
        $nbMediasForPodcastable = 2;
        $podcastableToDelete = factory(Channel::class)->create();
        // creating fake file with real file (storage disk is faked above).
        Storage::put(
            $podcastableToDelete->remoteFilePath(),
            file_get_contents(base_path('tests/fixtures/lemug.xml'))
        );

        // associating some medias for channel
        $this->createMediaWithFileForChannel($podcastableToDelete, $nbMediasForPodcastable);
        $podcastableToDelete->refresh();

        // dispatching media deletion
        PodcastableCleaning::dispatch($podcastableToDelete);

        // all media informations should be null
        Bus::assertDispatched(MediaCleaning::class, $nbMediasForPodcastable);
    }
}
