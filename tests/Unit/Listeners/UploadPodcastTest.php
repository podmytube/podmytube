<?php

namespace Tests\Unit\Listeners;

use App\Channel;
use App\Media;
use App\Podcast\PodcastBuilder;
use App\Podcast\PodcastUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UploadPodcastTest extends TestCase
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
    }

    public function testRefreshPodcastJobIsWorkingFine()
    {
        Event::fake();

        PodcastBuilder::forChannel($this->channel)->build()->save();

        $this->assertTrue(
            PodcastUpload::prepare($this->channel)->remoteFeedExists(),
            'It seems podcast feed does not exists on remote'
        );
    }
}
