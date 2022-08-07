<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Podcast\PodcastItems;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PodcastItemsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Models\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        Artisan::call('view:clear');
    }

    public function test_no_items_should_render_empty(): void
    {
        $podcastItems = $this->channel->podcastItems();
        $this->assertEmpty(PodcastItems::with($podcastItems)->render());
    }

    public function test_rendering_podcast_is_working_fine(): void
    {
        // adding some medias
        $this->addMediasToChannel($this->channel, 5, true);

        $podcastItems = $this->channel->podcastItems();

        $result = PodcastItems::with($podcastItems)->render();

        foreach ($podcastItems as $podcastItem) {
            $this->assertStringContainsString('<guid>' . $podcastItem->guid . '</guid>', $result);
            $this->assertStringContainsString('<title>' . $podcastItem->title . '</title>', $result);
            $this->assertStringContainsString(
                '<enclosure url="' . $podcastItem->enclosureUrl . '" length="' . $podcastItem->mediaLength . '" type="audio/mpeg" />',
                $result
            );
            $this->assertStringContainsString('<pubDate>' . $podcastItem->pubDate . '</pubDate>', $result);
            $this->assertStringContainsString('<itunes:duration>' . $podcastItem->duration . '</itunes:duration>', $result);
            $this->assertStringContainsString('<itunes:explicit>' . $podcastItem->explicit . '</itunes:explicit>', $result);
        }
    }
}
