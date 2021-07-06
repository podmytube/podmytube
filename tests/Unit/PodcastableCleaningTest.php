<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Jobs\MediaCleaning;
use App\Jobs\PodcastableCleaning;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
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
        Bus::fake();
    }

    /** @test */
    public function channel_cleaning_is_working_fine(): void
    {
        $nbMediasForPodcastable = 2;
        $this->podcastableToDelete = factory(Channel::class)->create();
        // creating fake file with real file (storage disk is faked above).
        $this->createFakeRemoteFileForPodcast($this->podcastableToDelete);

        // associating some medias for channel
        $this->createMediaWithFileForChannel($this->podcastableToDelete, $nbMediasForPodcastable);
        $this->podcastableToDelete->refresh();
        $savedYoutubeId = $this->podcastableToDelete->youtubeId();

        // dispatching media deletion
        $job = new PodcastableCleaning($this->podcastableToDelete);
        $job->handle();

        // media clening should have been dispatched twice.
        Bus::assertDispatched(MediaCleaning::class, $nbMediasForPodcastable);
        // podcast file should be deleted
        $this->assertFalse(Storage::exists($this->podcastableToDelete->remoteFilePath()));
        // podcastable should be deleted from db
        $this->assertNull(Channel::byYoutubeId($savedYoutubeId));
    }

    /** @test */
    public function playlist_cleaning_is_working_fine(): void
    {
        $nbMediasForPodcastable = 2;
        $this->podcastableToDelete = $this->createPlaylistWithMedia();

        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $medias = $this->podcastableToDelete->associatedMedias();
        $this->assertCount(2, $medias);

        $medias->map(function ($media): void {
            /*
             * for each "grabbed" medias that is present in this playlist
             * I want to upload some fake file
             * only to check they are really deleted by PodcastableCleaning
             */
            $this->createFakeRemoteFileForMedia($media);
            $this->assertTrue(Storage::exists($media->remoteFilePath()));
        });

        // creating fake file with real file (storage disk is faked above).
        $this->createFakeRemoteFileForPodcast($this->podcastableToDelete);
        $this->assertTrue(Storage::exists($this->podcastableToDelete->remoteFilePath()));
        $savedYoutubeId = $this->podcastableToDelete->youtubeId();

        // running podcast deletion
        $job = new PodcastableCleaning($this->podcastableToDelete);
        $job->handle();

        // media clening should have been dispatched twice.
        Bus::assertDispatched(MediaCleaning::class, $nbMediasForPodcastable);
        // podcast file should be deleted
        $this->assertFalse(Storage::exists($this->podcastableToDelete->remoteFilePath()));
        // podcastable should be deleted from db
        $this->assertNull(Playlist::byYoutubeId($savedYoutubeId));
    }
}
