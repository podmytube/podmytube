<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Channel;
use App\Jobs\ChannelCleaningJob;
use App\Jobs\MediaCleaning;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\Playlist;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelCleaningJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channelToDelete;

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
        $this->channelToDelete = $this->createChannelWithPlan();
        // creating fake file with real file (storage disk is faked above).
        $this->createFakeRemoteFileForPodcast($this->channelToDelete);

        // associating some medias for channel
        $this->createMediaWithFileForChannel($this->channelToDelete, $nbMediasForPodcastable);
        $this->channelToDelete->refresh();
        $savedYoutubeId = $this->channelToDelete->youtubeId();

        // associate playlist with this channel
        $playlistThatShouldBeRemoved = factory(Playlist::class)->create(['channel_id' => $this->channelToDelete->youtubeId()]);
        $this->channelToDelete->refresh();

        $this->assertCount(1, $this->channelToDelete->playlists);

        // dispatching media deletion
        $job = new ChannelCleaningJob($this->channelToDelete);
        $job->handle();

        // media clening should have been dispatched twice.
        Bus::assertDispatched(MediaCleaning::class, $nbMediasForPodcastable);

        // podcast file should be deleted
        $this->assertFalse(Storage::exists($this->channelToDelete->remoteFilePath()));

        // object that should have been deleted from db
        $this->assertNull(Channel::byYoutubeId($savedYoutubeId));

        $this->assertNull(Playlist::byYoutubeId($playlistThatShouldBeRemoved->youtubeId()));
        $this->assertNull(Subscription::byChannelId($this->channelToDelete->youtubeId()));
    }
}
