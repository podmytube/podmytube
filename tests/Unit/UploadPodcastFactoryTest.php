<?php

namespace Tests\Unit;

use App\Factories\UploadPodcastFactory;
use App\Jobs\SendFileBySFTP;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->channel = $this->createChannelWithPlan();
    }

    public function testfoo()
    {
        Bus::fake(SendFileBySFTP::class);
        $factory = UploadPodcastFactory::init()->forChannel($this->channel);

        $this->assertEquals(
            $this->channel->channel_id . '-' . config('app.feed_filename'),
            $factory->localFilename()
        );
        $this->assertEquals(
            '/app/tmp/' . $this->channel->channel_id . '-' . config('app.feed_filename'),
            $factory->localPath()
        );
        $this->assertEquals($this->channel->remoteFilePath(), $factory->remotePath());
        Bus::assertDispatched(SendFileBySFTP::class);
    }
}
