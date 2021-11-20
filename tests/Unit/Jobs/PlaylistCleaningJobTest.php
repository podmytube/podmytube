<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\MediaCleaning;
use App\Jobs\PlaylistCleaningJob;
use App\Jobs\PodcastableCleaning;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlaylistCleaningJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Playlist */
    protected $playlistToDelete;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake(SendFileBySFTP::REMOTE_DISK);
        Bus::fake();
    }

    /** @test */
    public function playlist_cleaning_is_working_fine(): void
    {
        $nbMediasForPodcastable = 2;
        $this->playlistToDelete = $this->createPlaylistWithMedia();

        $this->seedApiKeys();
        $medias = $this->playlistToDelete->associatedMedias();
        $this->assertCount($nbMediasForPodcastable, $medias);

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
        $this->createFakeRemoteFileForPodcast($this->playlistToDelete);
        $this->assertTrue(Storage::exists($this->playlistToDelete->remoteFilePath()));
        $savedYoutubeId = $this->playlistToDelete->youtubeId();

        // running podcast deletion
        $job = new PlaylistCleaningJob($this->playlistToDelete);
        $job->handle();

        // media cleaning should NOT have been dispatched.
        Bus::assertNotDispatched(MediaCleaning::class);
        // podcast file should be deleted
        $this->assertFalse(Storage::exists($this->playlistToDelete->remoteFilePath()));
        // podcastable should be deleted from db
        $this->assertNull(Playlist::byYoutubeId($savedYoutubeId));
    }
}
