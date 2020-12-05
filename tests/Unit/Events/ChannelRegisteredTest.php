<?php

namespace Tests\Unit\Events;

use App\Channel;
use App\Events\ChannelRegistered;
use App\Events\PodcastUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChannelRegisteredTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp():void
    {
        parent::setUp();
        /** creating fake channel with 2 medias */
        $this->channel = factory(Channel::class)->create(['channel_id' => 'test']);
    }

    public function testEventIsWorkingFine()
    {
        Event::fake();

        ChannelRegistered::dispatch($this->channel);

        Event::assertDispatched(PodcastUpdated::class);
    }
}
