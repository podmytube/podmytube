<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Exceptions\PodcastItemNotValidException;
use App\Models\Channel;
use App\Models\Media;
use App\Podcast\PodcastItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PodcastItemTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->media = Media::factory()->grabbedAt(now())->create();
    }

    public function test_no_title_should_throw_exception(): void
    {
        $this->expectException(PodcastItemNotValidException::class);
        $itemData = $this->media->toPodcastItem();
        $itemData['title'] = null;
        PodcastItem::with($itemData)->render();
    }

    public function test_no_enclosure_url_should_throw_exception(): void
    {
        $itemData = $this->media->toPodcastItem();
        $itemData['enclosureUrl'] = null;
        $this->expectException(PodcastItemNotValidException::class);
        PodcastItem::with($itemData)->render();
    }

    /** @test */
    public function negative_media_length_should_throw_exception(): void
    {
        $itemData = $this->media->toPodcastItem();
        $itemData['mediaLength'] = -12;
        $this->expectException(PodcastItemNotValidException::class);
        PodcastItem::with($itemData)->render();
    }

    public function test_explicit_podcast_item_is_fine(): void
    {
        $channel = Channel::factory()->create(['explicit' => true]);
        $this->media = Media::factory()->grabbedAt(now())->create(['channel_id' => $channel->channel_id]);
        $renderedItem = PodcastItem::with($this->media->toPodcastItem())->render();
        $this->assertStringContainsString('<itunes:explicit>true</itunes:explicit>', $renderedItem);
    }

    public function test_not_explicit_podcast_item_is_fine(): void
    {
        $channel = Channel::factory()->create(['explicit' => false]);
        $this->media = Media::factory()->grabbedAt(now())->create(['channel_id' => $channel->channel_id]);
        $renderedItem = PodcastItem::with($this->media->toPodcastItem())->render();
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $renderedItem);
    }

    public function test_podcast_item_is_fine(): void
    {
        $renderedItem = PodcastItem::with($this->media->toPodcastItem())->render();

        $this->assertStringContainsString('<item>', $renderedItem);
        $this->assertStringContainsString('<guid>' . $this->media->media_id . '</guid>', $renderedItem);
        $this->assertStringContainsString('<title>' . $this->media->title . '</title>', $renderedItem);
        $this->assertStringContainsString(
            '<enclosure url="' .
                    $this->media->enclosureUrl() .
                    '" length="' .
                    $this->media->length .
                    '" type="audio/mpeg" />',
            $renderedItem
        );
        $this->assertStringContainsString('<pubDate>' . $this->media->pubDate() . '</pubDate>', $renderedItem);
        $this->assertStringContainsString('<itunes:duration>' . $this->media->duration() . '</itunes:duration>', $renderedItem);

        $this->assertStringContainsString(
            "<itunes:explicit>{$this->media->channel->podcastExplicit()}</itunes:explicit>",
            $renderedItem
        );
        $this->assertStringContainsString('</item>', $renderedItem);
    }
}
