<?php

namespace Tests\Unit;

use App\Channel;
use Carbon\Carbon;
use Tests\TestCase;
use App\Podcast\PodcastBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelModelTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel channel model */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testCreatedAt()
    {
        $this->assertNotNull($this->channel->createdAt());
        $this->assertInstanceOf(Carbon::class, $this->channel->createdAt());
    }

    public function testingPodcastUrl()
    {
        $this->assertEquals(
            getenv('PODCASTS_URL') .
                DIRECTORY_SEPARATOR .
                $this->channel->channelId() .
                DIRECTORY_SEPARATOR .
                PodcastBuilder::FEED_FILENAME,
            $this->channel->podcastUrl()
        );
    }
}
