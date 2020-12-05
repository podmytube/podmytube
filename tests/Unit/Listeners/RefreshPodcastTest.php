<?php

namespace Tests\Unit\Listeners;

use App\Channel;
use App\Events\ChannelUpdated;
use App\Events\PodcastUpdated;
use App\Listeners\RefreshPodcast;
use App\Media;
use App\Podcast\PodcastBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RefreshPodcastTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp():void
    {
        parent::setUp();
        /** creating fake channel with 2 medias */
        $this->channel = factory(Channel::class)->create(['channel_id' => 'test']);
        factory(Media::class, 2)->create(['channel_id' => $this->channel]);

        /** checking podcast feed does not exist */
        $feedPath = PodcastBuilder::forChannel($this->channel)->path();

        if (file_exists($feedPath)) {
            /** if exists delete */
            unlink($feedPath);
        }
    }

    public function testRefreshPodcastJobIsWorkingFine()
    {
        /** events are fired but not executed */
        Event::fake();

        /** faking firing ChannelUpdatedEvent */
        $event = new ChannelUpdated($this->channel);

        /** using listener with created event */
        (new RefreshPodcast($event))->handle($event);

        /** Listener should create the feed and dispatch PodcastUpdated event  */
        $this->assertFileExists(PodcastBuilder::forChannel($this->channel)->path());
        Event::assertDispatched(PodcastUpdated::class);
    }
}
