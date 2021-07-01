<?php

declare(strict_types=1);

namespace Tests\Unit\Podcast;

use App\Channel;
use App\Exceptions\PodcastItemNotValidException;
use App\Media;
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

    /** @var \App\Media */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->media = factory(Media::class)->create(['grabbed_at' => now()]);
    }

    public function testNoTitleShouldThrowException(): void
    {
        $this->expectException(PodcastItemNotValidException::class);
        $itemData = $this->media->toPodcastItem();
        $itemData['title'] = null;
        PodcastItem::with($itemData)->render();
    }

    public function testNoEnclosureUrlShouldThrowException(): void
    {
        $itemData = $this->media->toPodcastItem();
        $itemData['enclosureUrl'] = null;
        $this->expectException(PodcastItemNotValidException::class);
        PodcastItem::with($itemData)->render();
    }

    /** @test */
    public function negativeMediaLengthShouldThrowException(): void
    {
        $itemData = $this->media->toPodcastItem();
        $itemData['mediaLength'] = -12;
        $this->expectException(PodcastItemNotValidException::class);
        PodcastItem::with($itemData)->render();
    }

    public function testExplicitPodcastItemIsFine(): void
    {
        $channel = factory(Channel::class)->create(['explicit' => true]);
        $this->media = factory(Media::class)->create(['channel_id' => $channel->channel_id, 'grabbed_at' => now()]);
        $renderedItem = PodcastItem::with($this->media->toPodcastItem())->render();
        $this->assertStringContainsString('<itunes:explicit>true</itunes:explicit>', $renderedItem);
    }

    public function testNotExplicitPodcastItemIsFine(): void
    {
        $channel = factory(Channel::class)->create(['explicit' => false]);
        $this->media = factory(Media::class)->create(['channel_id' => $channel->channel_id, 'grabbed_at' => now()]);
        $renderedItem = PodcastItem::with($this->media->toPodcastItem())->render();
        $this->assertStringContainsString('<itunes:explicit>false</itunes:explicit>', $renderedItem);
    }

    public function testPodcastItemIsFine(): void
    {
        $renderedItem = PodcastItem::with($this->media->toPodcastItem())->render();

        $this->assertStringContainsString('<item>', $renderedItem);
        $this->assertStringContainsString('<guid>'.$this->media->media_id.'</guid>', $renderedItem);
        $this->assertStringContainsString('<title>'.$this->media->title.'</title>', $renderedItem);
        $this->assertStringContainsString(
            '<enclosure url="'.
                    $this->media->enclosureUrl().
                    '" length="'.
                    $this->media->length.
                    '" type="audio/mpeg" />',
            $renderedItem
        );
        $this->assertStringContainsString('<pubDate>'.$this->media->pubDate().'</pubDate>', $renderedItem);
        $this->assertStringContainsString('<itunes:duration>'.$this->media->duration().'</itunes:duration>', $renderedItem);

        $this->assertStringContainsString(
            "<itunes:explicit>{$this->media->channel->podcastExplicit()}</itunes:explicit>",
            $renderedItem
        );
        $this->assertStringContainsString('</item>', $renderedItem);
    }
}
