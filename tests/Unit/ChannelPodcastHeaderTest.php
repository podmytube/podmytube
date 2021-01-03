<?php

namespace Tests\Unit;

use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelPodcastHeaderTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testingToPodcastHeaderIsFine()
    {
        $expectedKeys = [
            'title',
            'link',
            'description',
            'coverUrl',
            'language',
            'category',
            'explicit',
        ];
        $result = $this->channel->podcastHeader();
        array_map(function ($key) use ($result) {
            $this->assertArrayHasKey($key, $result, "Converting a channel to a podcast header should have key {$key}.");
        }, $expectedKeys);

        $this->assertEquals($result['title'], $this->channel->title);
        $this->assertEquals($result['link'], $this->channel->link);
        $this->assertEquals($result['description'], $this->channel->description);
        
    }
}
