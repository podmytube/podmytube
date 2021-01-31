<?php

namespace Tests\Unit\Podcast;

use App\Category;
use App\Channel;
use App\Podcast\PodcastHeader;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Traits\IsAbleToTestPodcast;

class PodcastHeaderTest extends TestCase
{
    use RefreshDatabase, IsAbleToTestPodcast;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        Artisan::call('db:seed', ['--class' => 'CategoriesTableSeeder']);
    }

    public function testingNewChannelWithParentCategoryShouldRenderFine()
    {
        $attributes = [
            'link' => $this->channel->link,
            'title' => $this->channel->title(),
            'description' => $this->channel->description,
            'copyright' => $this->channel->podcast_copyright,
            'language' => $this->channel->language->code,
            'imageUrl' => $this->channel->podcastCoverUrl(),
            'category' => Category::bySlug('fashion-beauty'),
            'title' => $this->channel->title(),
            'link' => $this->channel->link,
            'explicit' => $this->channel->podcastExplicit(),
        ];

        $rendered = PodcastHeader::create($attributes)->render();
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $rendered);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $rendered);
        $this->assertStringContainsString('<description><![CDATA[' . $this->channel->description . ']]></description>', $rendered);
        $this->assertStringContainsString('<copyright>' . $this->channel->podcast_copyright . '</copyright>', $rendered);
        $this->assertStringContainsString('<language>' . $this->channel->language->code . '</language>', $rendered);
        $this->assertStringContainsString('<image>', $rendered);
        $this->assertStringContainsString('<url>' . $this->channel->podcastCoverUrl() . '</url>', $rendered);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $rendered);
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $rendered);
        $this->assertStringContainsString('</image>', $rendered);
        $this->assertStringContainsString('<itunes:category text="Arts">', $rendered);
        $this->assertStringContainsString('<itunes:category text="Fashion &amp; Beauty"', $rendered);
        $this->assertStringContainsString('</itunes:category>', $rendered);
        $this->assertStringContainsString('<itunes:explicit>' . $this->channel->podcastExplicit() . '</itunes:explicit>', $rendered);
    }

    public function testingNewChannelWithSimpleCategoryShouldRenderFine()
    {
        $attributes = [
            'link' => $this->channel->link,
            'title' => $this->channel->title(),
            'description' => $this->channel->description,
            'copyright' => $this->channel->podcast_copyright,
            'language' => $this->channel->language->code,
            'imageUrl' => $this->channel->podcastCoverUrl(),
            'category' => Category::bySlug('health-fitness'),
            'title' => $this->channel->title(),
            'link' => $this->channel->link,
        ];

        $rendered = PodcastHeader::create($attributes)->render();
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $rendered);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $rendered);
        $this->assertStringContainsString('<description><![CDATA[' . $this->channel->description . ']]></description>', $rendered);
        $this->assertStringContainsString('<copyright>' . $this->channel->podcast_copyright . '</copyright>', $rendered);
        $this->assertStringContainsString('<language>' . $this->channel->language->code . '</language>', $rendered);
        $this->assertStringContainsString('<image>', $rendered);
        $this->assertStringContainsString('<url>' . $this->channel->podcastCoverUrl() . '</url>', $rendered);
        $this->assertStringContainsString('<title>' . $this->channel->title() . '</title>', $rendered);
        $this->assertStringContainsString('<link>' . $this->channel->link . '</link>', $rendered);
        $this->assertStringContainsString('<itunes:category text="Health &amp; Fitness" />', $rendered);
        $this->assertStringContainsString('</image>', $rendered);
    }
}
