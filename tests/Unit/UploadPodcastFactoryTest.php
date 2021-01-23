<?php

namespace Tests\Unit;

use App\Factories\UploadPodcastFactory;
use App\Jobs\SendFileBySFTP;
use App\Media;
use App\Playlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UploadPodcastFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake(SendFileBySFTP::class);
    }

    public function testBuildingPodcastForChannelIsGood()
    {
        $this->channel = $this->createChannelWithPlan();
        $factory = UploadPodcastFactory::init()->for($this->channel);

        $this->assertEquals(
            '/app/tmp/' . $this->channel->relativeFeedPath(),
            $factory->localPath()
        );
        $this->assertEquals($this->channel->remoteFilePath(), $factory->remotePath());
        Bus::assertDispatched(SendFileBySFTP::class);
    }

    public function testBuildingPodcastForPlaylistIsGood()
    {
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        factory(Media::class)->create(['media_id' => 'GJzweq_VbVc', 'grabbed_at' => now()->subday()]);
        factory(Media::class)->create(['media_id' => 'AyU4u-iQqJ4', 'grabbed_at' => now()->subWeek()]);
        factory(Media::class)->create(['media_id' => 'hb0Fo1Jqxkc']);

        $this->playlist = factory(Playlist::class)->create(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);
        $factory = UploadPodcastFactory::init()->for($this->playlist);

        $this->assertEquals(
            '/app/tmp/' . $this->playlist->relativeFeedPath(),
            $factory->localPath()
        );
        $this->assertEquals($this->playlist->remoteFilePath(), $factory->remotePath());
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
