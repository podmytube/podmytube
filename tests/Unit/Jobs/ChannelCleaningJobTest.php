<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\ChannelCleaningJob;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Playlist;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelCleaningJobTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channelToDelete;

    public function setUp(): void
    {
        parent::setUp();
        // Storage::fake(Channel::REMOTE_DISK);
        // Bus::fake();
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
        $savedYoutubeId = $this->channelToDelete->youtube_id;

        // associate playlist with this channel
        $playlistThatShouldBeRemoved = Playlist::factory()->create(['channel_id' => $this->channelToDelete->youtube_id]);
        $this->channelToDelete->refresh();

        $this->assertCount(1, $this->channelToDelete->playlists);

        // dispatching channels deletion
        $job = new ChannelCleaningJob($this->channelToDelete);
        $job->handle();

        // folders should have been deleted
        $this->assertFalse(
            Storage::exists($this->channelToDelete->feedFolderPath()),
            $this->channelToDelete->feedFolderPath() . ' should have been removed.'
        );
        $this->assertFalse(
            Storage::exists($this->channelToDelete->mp3FolderPath()),
            $this->channelToDelete->mp3FolderPath() . ' should have been removed.'
        );
        $this->assertFalse(
            Storage::exists($this->channelToDelete->playlistFolderPath()),
            $this->channelToDelete->playlistFolderPath() . ' should have been removed.'
        );
        $this->assertFalse(
            Storage::exists($this->channelToDelete->coverFolderPath()),
            $this->channelToDelete->coverFolderPath() . ' should have been removed.'
        );

        $this->assertNull(Playlist::byYoutubeId($playlistThatShouldBeRemoved->youtube_id));
        $this->assertNull(Subscription::byChannelId($this->channelToDelete->youtube_id));
        $this->assertCount(
            0,
            Media::query()->where('channel_id', '=', $this->channelToDelete->youtube_id)->get()
        );

        // object that should have been deleted from db
        $this->assertNull(Channel::byYoutubeId($savedYoutubeId));
    }
}
