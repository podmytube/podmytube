<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\ThumbUpdated;
use App\Jobs\SendFileBySFTP;
use App\Listeners\UploadPodcast;
use App\Media;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UploadPodcastListenerTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel */
    protected $channel;

    /** @var \App\Playlist */
    protected $playlist;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->channel = $this->createChannelWithPlan();
        $this->playlist = factory(Playlist::class)->create([
            'channel_id' => $this->channel->channelId(),
            'youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID,
        ]);
        factory(Media::class)->create(['media_id' => 'GJzweq_VbVc', 'grabbed_at' => now()->subday()]);
        factory(Media::class)->create(['media_id' => 'AyU4u-iQqJ4', 'grabbed_at' => now()->subWeek()]);
        factory(Media::class)->create(['media_id' => 'hb0Fo1Jqxkc']);
        Bus::fake();
    }

    /** @test */
    public function upload_podcast_listener_for_channel(): void
    {
        $thumbUpdatedEvent = new ThumbUpdated($this->channel);
        $this->assertTrue((new UploadPodcast())->handle($thumbUpdatedEvent));
        Bus::assertDispatched(function (SendFileBySFTP $job) {
            return config('app.feed_path') . $this->channel->channelId() . '/' . config('app.feed_filename') === $job->remoteFilePath;
        });
    }

    /** @test */
    public function upload_podcast_listener_for_playlist(): void
    {
        $thumbUpdatedEvent = new ThumbUpdated($this->playlist);
        $this->assertTrue((new UploadPodcast())->handle($thumbUpdatedEvent));
        // Bus::assertDispatched(SendFileBySFTP::class); // useless
        Bus::assertDispatched(function (SendFileBySFTP $job) {
            return config('app.playlists_path') . $this->playlist->channelId() . '/' . $this->playlist->youtube_playlist_id . '.xml' === $job->remoteFilePath;
        });
    }
}
