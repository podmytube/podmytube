<?php

namespace Tests\Unit;

use App\Media;
use App\Channel;
use App\Podcast\PodcastItems;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastItemsTest extends TestCase
{
    use RefreshDatabase;

    protected static $channel;

    public function setUp(): void
    {
        parent::setUp();
        self::$channel = factory(Channel::class)->create();
    }

    public function testWithMoreMedias()
    {
        $medias = factory(Media::class, 5)->create(['channel_id' => self::$channel->channel_id]);
        $renderedItems = PodcastItems::prepare(self::$channel)->render();

        foreach ($medias as $media) {
            $this->assertStringContainsString("<guid>" . $media->media_id . "</guid>", $renderedItems);
            $this->assertStringContainsString("<title>" . $media->title . "</title>", $renderedItems);
            $this->assertStringContainsString("<enclosure url=\"" . $media->enclosureUrl() . "\" length=\"" . $media->length . "\" type=\"audio/mpeg\" />", $renderedItems);
            $this->assertStringContainsString("<pubDate>" . $media->pubDate() . "</pubDate>", $renderedItems);

            $this->assertStringContainsString("<itunes:duration>" . $media->duration() . "</itunes:duration>", $renderedItems);
            $this->assertStringContainsString("<itunes:explicit>" . $media->channel->explicit() . "</itunes:explicit>", $renderedItems);
        }
    }

    public function testWithOneMedia()
    {
        $media = factory(Media::class)->create(['channel_id' => self::$channel->channel_id])->first();
        $renderedItems = PodcastItems::prepare(self::$channel)->render();
        $this->assertStringContainsString("<guid>" . $media->media_id . "</guid>", $renderedItems);
        $this->assertStringContainsString("<title>" . $media->title . "</title>", $renderedItems);
        $this->assertStringContainsString("<enclosure url=\"" . $media->enclosureUrl() . "\" length=\"" . $media->length . "\" type=\"audio/mpeg\" />", $renderedItems);
        $this->assertStringContainsString("<pubDate>" . $media->pubDate() . "</pubDate>", $renderedItems);

        $this->assertStringContainsString("<itunes:duration>" . $media->duration() . "</itunes:duration>", $renderedItems);
        $this->assertStringContainsString("<itunes:explicit>" . $media->channel->explicit() . "</itunes:explicit>", $renderedItems);
    }
}
