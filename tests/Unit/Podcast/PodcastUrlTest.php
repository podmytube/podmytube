<?php

namespace Tests\Unit\Podcast;

use App\Channel;
use App\Podcast\PodcastUrl;
use Tests\TestCase;

class PodcastUrlTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGettingUrlIsFine()
    {
        $channel = factory(Channel::class)->create();
        $this->assertEquals(
            config('app.podcasts_url') . '/' . $channel->channelId() . '/' . PodcastUrl::FEED_FILENAME,
            PodcastUrl::prepare($channel)->get()
        );
    }
}
