<?php

namespace Tests\Unit;

use App\Channel;
use Carbon\Carbon;
use Tests\TestCase;
use App\Podcast\PodcastBuilder;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\ChannelCreationOnlyYoutubeIsAccepted;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;

class ChannelModelTest extends TestCase
{
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

    public function testingHasFilterShouldReturnFalse()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => null,
            'reject_video_by_keyword' => null,
            'reject_video_too_old' => null,
        ]);
        $this->assertFalse($channel->hasFilter());
    }

    public function testingHasFilterShouldReturnTrue()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'chat',
            'reject_video_by_keyword' => null,
            'reject_video_too_old' => null,
        ]);
        $this->assertTrue($channel->hasFilter());
    }

    public function testingHasFilterShouldReturnTrueToo()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'chat',
            'reject_video_by_keyword' => null,
            'reject_video_too_old' => '2011-03-6',
        ]);
        $this->assertTrue($channel->hasFilter());
    }

    public function testingHasFilterShouldReturnTrueAgain()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'chat',
            'reject_video_by_keyword' => 'my keyword',
            'reject_video_too_old' => '06/03/2001',
        ]);
        $this->assertTrue($channel->hasFilter());
    }

    public function testingGetFiltersReturnNothing()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => null,
            'reject_video_by_keyword' => null,
            'reject_video_too_old' => null,
        ]);
        $this->assertEmpty($channel->getFilters());
    }

    public function testingGetFiltersReturnTagSet()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'chat',
            'reject_video_by_keyword' => null,
            'reject_video_too_old' => null,
        ]);
        $this->assertCount(1, $channel->getFilters());
    }

    public function testingGetFiltersReturnTagAndKeywordSet()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'cat',
            'reject_video_by_keyword' => 'dolphin',
            'reject_video_too_old' => null,
        ]);
        $this->assertCount(2, $channel->getFilters());
    }

    public function testingGetFiltersReturnAllFiltersSet()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'cat',
            'reject_video_by_keyword' => 'dolphin',
            'reject_video_too_old' => '06/03/2011',
        ]);
        $this->assertCount(3, $channel->getFilters());
    }
}
