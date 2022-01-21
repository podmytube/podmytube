<?php

namespace Tests\Unit\Podcast;

use App\Podcast\PodcastBuilder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\IsAbleToTestPodcast;

class PodcastBuilderTest extends TestCase
{
    use RefreshDatabase,
        WithFaker,
        IsAbleToTestPodcast;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Media $medias */
    protected $medias;

    /** @var string $renderedPodcast */
    protected $renderedPodcast;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->createCoverFor($this->channel);
    }

    public function testRenderingPodcastWithoutItemsShouldBeGood()
    {
        $this->headerChecking($this->channel, PodcastBuilder::create($this->channel->toPodcast())->render());
    }

    public function testRenderingPodcastWithItemsShouldBeGoodToo()
    {
        $this->addMediasToChannel($this->channel, 5, true);
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast);
    }

    public function testRenderingExplicitChannelShouldBeGood()
    {
        $this->channel->explicit = true;
        $this->addMediasToChannel($this->channel, 3, true);
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast);
    }
}
