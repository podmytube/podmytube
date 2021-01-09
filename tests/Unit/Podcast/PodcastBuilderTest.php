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
            $this->assertStringContainsString('<itunes:explicit>' . $media->explicit === true ? 'true' : 'false' . '</itunes:explicit>', $this->renderedPodcast);
        });
    }

    public function headerChecking()
    {
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
    }
}
