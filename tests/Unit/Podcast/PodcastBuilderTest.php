<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Podcast\PodcastBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\IsAbleToTestPodcast;

/**
 * @internal
 *
 * @coversNothing
 */
class PodcastBuilderTest extends TestCase
{
    use IsAbleToTestPodcast;
    use RefreshDatabase;
    use WithFaker;

    protected Channel $channel;

    /** @var string */
    protected $renderedPodcast;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan(Plan::factory()->name('starter')->create());
        $this->createCoverFor($this->channel);
    }

    public function test_rendering_podcast_without_items_should_be_good(): void
    {
        $this->headerChecking($this->channel, PodcastBuilder::create($this->channel->toPodcast())->render());
    }

    public function test_rendering_podcast_with_items_should_be_good_too(): void
    {
        $expectedNumberOfMedias = 5;
        $this->addMediasToChannel(channel: $this->channel, numberOfMediasToAdd: $expectedNumberOfMedias, grabbed: true);
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast, $expectedNumberOfMedias);
    }

    public function test_rendering_explicit_channel_should_be_good(): void
    {
        $expectedNumberOfMedias = 3;
        $this->channel->explicit = true;
        $this->addMediasToChannel($this->channel, 3, true);
        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast, $expectedNumberOfMedias);
    }

    public function test_rendering_podcast_with_deleted_items_should_be_good_too(): void
    {
        $expectedNumberOfMedias = 3;
        $this->addMediasToChannel(channel: $this->channel, numberOfMediasToAdd: $expectedNumberOfMedias, grabbed: true);

        // adding deleted media to channel - should not be in podcast
        $deletedMedia = Media::factory()->channel($this->channel)->create();
        $deletedMedia->delete();

        $renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking($this->channel, $renderedPodcast);
        $this->itemsChecking($this->channel, $renderedPodcast, $expectedNumberOfMedias);
    }
}
