<?php

namespace Tests\Unit\Podcast;

use App\Thumb;
use App\Podcast\PodcastBuilder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PodcastBuilderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $channel;
    protected $medias;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        $this->addMediasToChannel($this->channel, 5, true);
        factory(Thumb::class)->create(['channel_id' => $this->channel->channel_id, ]);
    }

    public function testRenderingWholePodcast()
    {
        $attributes = [
        ];
        $renderedPodcast = PodcastBuilder::create($attributes)->render();

        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $renderedPodcast);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $renderedPodcast);
        $this->assertStringContainsString('<description><![CDATA[' . $this->channel->description . ']]></description>', $renderedPodcast);
        $this->assertStringContainsString('<copyright>' . $this->channel->podcast_copyright . '</copyright>', $renderedPodcast);
        $this->assertStringContainsString('<language>' . $this->channel->language->code . '</language>', $renderedPodcast);
        $this->assertStringContainsString('<image>', $renderedPodcast);
        $this->assertStringContainsString('<url>' . $this->channel->thumb->podcastUrl() . '</url>', $renderedPodcast);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $renderedPodcast);
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $renderedPodcast);
        $this->assertStringContainsString('</image>', $renderedPodcast);
    }
}
