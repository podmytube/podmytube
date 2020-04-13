<?php

namespace Tests\Unit;

use App\Channel;
use App\Podcast\PodcastUrl;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            getenv('PODCASTS_URL') .
                DIRECTORY_SEPARATOR .
                $channel->channelId() .
                DIRECTORY_SEPARATOR .
                PodcastUrl::FEED_FILENAME,
            PodcastUrl::prepare($channel)->get()
        );
    }
}
