<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Factories\UploadPodcastFactory;
use App\Interfaces\Podcastable;
use App\Jobs\SendFileBySFTP;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UploadPodcastFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(SendFileBySFTP::class);
    }

    public function test_building_podcast_for_channel_is_good(): void
    {
        $this->channel = $this->createChannelWithPlan();
        $factory = UploadPodcastFactory::for($this->channel)->run();

        $this->assertEquals($this->channel->remoteFilePath(), $factory->remotePath());
        Bus::assertDispatched(SendFileBySFTP::class);
    }

    public function test_building_podcast_for_playlist_is_good(): void
    {
        $this->seedApiKeys();
        Media::factory()->grabbedAt(now()->subDay())->create(['media_id' => 'GJzweq_VbVc']);
        Media::factory()->grabbedAt(now()->subDay())->create(['media_id' => 'AyU4u-iQqJ4']);
        Media::factory()->create(['media_id' => 'hb0Fo1Jqxkc']);

        /** @var Podcastable $playlist */
        $playlist = Playlist::factory()->create(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);
        $factory = UploadPodcastFactory::for($playlist)->run();

        $this->assertEquals($playlist->remoteFilePath(), $factory->remotePath());
        Bus::assertDispatched(SendFileBySFTP::class);
    }

    /** @test */
    public function prepare_local_path_for_channel_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan();
        $expected = '/tmp/' . now()->format('Y-m-d\THis') . '_channel_' . $this->channel->channelId();
        $factory = UploadPodcastFactory::for($this->channel);

        $this->assertEquals($expected, $factory->prepareLocalPath());
    }

    /** @test */
    public function prepare_local_path_for_playlist_is_fine(): void
    {
        /** @var Podcastable $playlist */
        $playlist = Playlist::factory()->create();
        $expected = '/tmp/' . now()->format('Y-m-d\THis') . '_playlist_' . $playlist->channelId();
        $factory = UploadPodcastFactory::for($playlist);

        $this->assertEquals($expected, $factory->prepareLocalPath());
    }
}
