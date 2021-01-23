<?php

namespace Tests\Unit\Podcast;

use App\Thumb;
use App\Podcast\PodcastBuilder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PodcastBuilderTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

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
        factory(Thumb::class)->create(['channel_id' => $this->channel->channel_id, ]);
    }

    public function testRenderingPodcastWithoutItemsShouldBeGood()
    {
        $this->renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking();
    }

    public function testRenderingPodcastWithItemsShouldBeGoodToo()
    {
        $this->addMediasToChannel($this->channel, 5, true);
        $this->renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking();
        $this->itemsChecking();
    }

    public function testRenderingExplicitChannelShouldBeGood()
    {
        $this->channel->explicit = true;
        $this->addMediasToChannel($this->channel, 3, true);
        $this->renderedPodcast = PodcastBuilder::create($this->channel->toPodcast())->render();
        $this->headerChecking();
        $this->itemsChecking();
    }

    public function itemsChecking()
    {
        $this->channel->mediasToPublish()->map(function ($media) {
            $this->assertStringContainsString('<guid>' . $media->media_id . '</guid>', $this->renderedPodcast);
            $this->assertStringContainsString('<title>' . $media->title . '</title>', $this->renderedPodcast);
            $this->assertStringContainsString(
                '<enclosure url="' . $media->enclosureUrl() . '" length="' . $media->length . '" type="audio/mpeg" />',
                $this->renderedPodcast
            );
            $this->assertStringContainsString('<pubDate>' . $media->pubDate() . '</pubDate>', $this->renderedPodcast);
            $this->assertStringContainsString('<itunes:duration>' . $media->duration() . '</itunes:duration>', $this->renderedPodcast);
            $this->assertStringContainsString(
                '<itunes:explicit>' . $this->channel->podcastExplicit() . '</itunes:explicit>',
                $this->renderedPodcast
            );
        });
    }

    public function headerChecking()
    {
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $this->renderedPodcast);
        $this->assertStringContainsString(
            '<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/">',
            $this->renderedPodcast
        );
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $this->renderedPodcast);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $this->renderedPodcast);
        $this->assertStringContainsString('<description><![CDATA[' . $this->channel->description . ']]></description>', $this->renderedPodcast);
        $this->assertStringContainsString('<copyright>' . $this->channel->podcast_copyright . '</copyright>', $this->renderedPodcast);
        $this->assertStringContainsString('<language>' . $this->channel->language->code . '</language>', $this->renderedPodcast);
        $this->assertStringContainsString('<image>', $this->renderedPodcast);
        $this->assertStringContainsString('<url>' . $this->channel->thumb->podcastUrl() . '</url>', $this->renderedPodcast);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $this->renderedPodcast);
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $this->renderedPodcast);
        $this->assertStringContainsString('</image>', $this->renderedPodcast);
        $this->assertStringContainsString('<itunes:owner>', $this->renderedPodcast);
        $this->assertStringContainsString('<itunes:email>' . $this->channel->email . '</itunes:email>', $this->renderedPodcast);
        $this->assertStringContainsString('</itunes:owner>', $this->renderedPodcast);
        //...
        $this->assertStringContainsString('</rss>', $this->renderedPodcast);
    }
}
