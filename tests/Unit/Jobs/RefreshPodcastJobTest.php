<?php

namespace Tests\Unit\Jobs;

use App\Channel;
use App\Jobs\RefreshPodcastJob;
use App\Podcast\PodcastBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RefreshPodcastJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp():void
    {
        parent::setUp();

        $this->channel = factory(Channel::class)->create(['channel_id' => 'test']);
        $feedPath = PodcastBuilder::forChannel($this->channel)->path();
        if (file_exists($feedPath)) {
            unlink($feedPath);
        }
    }

    public function testRefreshPodcastJobIsWorkingFine()
    {
        Event::fake();
        $RefreshPodcastJob = new RefreshPodcastJob($this->channel);
        $RefreshPodcastJob->handle();

        $this->assertFileExists(PodcastBuilder::forChannel($this->channel)->path());
    }
}
