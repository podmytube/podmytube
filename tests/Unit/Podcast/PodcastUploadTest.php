<?php

namespace Tests\Unit\Podcast;

use App\Media;
use App\Thumb;
use App\Channel;
use App\Exceptions\FeedDoesNotExistException;
use App\Podcast\PodcastUpload;
use App\Podcast\PodcastBuilder;
use Tests\TestCase;

class PodcastUploadTest extends TestCase
{
    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp():void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testUploadIsWorkingFine()
    {
        factory(Media::class, 3)->create(['channel_id' => $this->channel->channelId()]);
        factory(Thumb::class)->create(['channel_id' => $this->channel->channelId()]);
        PodcastBuilder::forChannel($this->channel)->build()->save();
        $this->assertTrue(PodcastUpload::prepare($this->channel)->upload());
    }

    public function testThrowExceptionWhenFeedDoesNotExists()
    {
        $this->expectException(FeedDoesNotExistException::class);
        PodcastUpload::prepare($this->channel);
    }
}
