<?php

namespace Tests\Unit;

use App\Media;
use App\Thumb;
use App\Channel;
use App\Exceptions\FeedDoesNotExist;
use App\Podcast\PodcastUpload;
use App\Podcast\PodcastBuilder;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastUploadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUploadIsWorkingFine()
    {
        $channel = factory(Channel::class)->create();
        factory(Media::class, 3)->create(['channel_id' => $channel->channelId()]);
        factory(Thumb::class)->create(['channel_id' => $channel->channelId()]);
        PodcastBuilder::prepare($channel)->save();

        $this->assertTrue(PodcastUpload::prepare($channel)->upload());
    }

    public function testThrowExceptionWhenFeedDoesNotExists()
    {
        $channel = factory(Channel::class)->create();
        $this->expectException(FeedDoesNotExist::class);
        PodcastUpload::prepare($channel);
    }

}
