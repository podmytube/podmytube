<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Podcast\PodcastBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\IsAbleToTestPodcast;

/**
 * @internal
 * @coversNothing
 */
class PodcastBuilderTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use IsAbleToTestPodcast;

    /** @var \App\Models\Channel */
    protected $channel;

    /** @var \App\Models\Media */
    protected $medias;

    /** @var string */
    protected $renderedPodcast;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->createCoverFor($this->channel);
    }

    public function test_rendering_podcast_without_items_should_be_good(): void
    {
        $this->headerChecking($this->channel, PodcastBuilder::create($this->channel->toPodcast())->render());
    }

    public function test_rendering_podcast_with_items_should_be_good_too(): void
    {
        $this->addMediasToChannel($this->channel, 5, true);
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast);
    }

    public function test_rendering_explicit_channel_should_be_good(): void
    {
        $this->channel->explicit = true;
        $this->addMediasToChannel($this->channel, 3, true);
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast);
    }
}
