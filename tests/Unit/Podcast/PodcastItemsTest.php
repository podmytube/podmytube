<?php

namespace Tests\Unit\Podcast;

use App\Podcast\PodcastItems;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PodcastItemsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        Artisan::call('view:clear');
    }

    public function testRenderingPodcastIsWorkingFine()
    {
        /** adding some medias */
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
