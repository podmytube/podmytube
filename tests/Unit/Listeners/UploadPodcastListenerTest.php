<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ThumbUpdatedEvent;
use App\Jobs\SendFileByRsync;
use App\Listeners\UploadPodcastListener;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UploadPodcastListenerTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    protected Playlist $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = Playlist::factory()->create([
            'channel_id' => $this->channel->channelId(),
            'youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID,
        ]);
        Media::factory()->grabbedAt(now()->subday())->create(['media_id' => 'GJzweq_VbVc']);
        Media::factory()->grabbedAt(now()->subWeek())->create(['media_id' => 'AyU4u-iQqJ4']);
        Media::factory()->create(['media_id' => 'hb0Fo1Jqxkc']);
        Bus::fake(SendFileByRsync::class);
    }

    /** @test */
    public function upload_podcast_listener_for_channel(): void
    {
        $thumbUpdatedEvent = new ThumbUpdatedEvent($this->channel);
        $this->assertTrue((new UploadPodcastListener())->handle($thumbUpdatedEvent));
        Bus::assertDispatched(function (SendFileByRsync $job) {
            return config('app.feed_path') . $this->channel->channelId() . '/' . config('app.feed_filename') === $job->remoteFilePath;
        });
    }

    /** @test */
    public function upload_podcast_listener_for_playlist(): void
    {
        $thumbUpdatedEvent = new ThumbUpdatedEvent($this->playlist);
        $this->assertTrue((new UploadPodcastListener())->handle($thumbUpdatedEvent));
        Bus::assertDispatched(function (SendFileByRsync $job) {
            return config('app.playlists_path') . $this->playlist->channelId() . '/' . $this->playlist->youtube_playlist_id . '.xml' === $job->remoteFilePath;
        });
    }
}
