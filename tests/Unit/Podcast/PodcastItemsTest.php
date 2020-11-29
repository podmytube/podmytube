<?php

namespace Tests\Unit\Podcast;

use App\Channel;
use App\Media;
use App\Podcast\PodcastItems;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PodcastItemsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        Artisan::call('view:clear');
    }

    public function testWithMoreMedias()
    {
        $medias = factory(Media::class, 5)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => $this->faker->dateTimeBetween(Carbon::parse('1 year ago'), Carbon::now())
        ]);
        $renderedItems = PodcastItems::prepare($this->channel)->render();

        foreach ($medias as $media) {
            $this->assertStringContainsString(
                '<guid>' . $media->media_id . '</guid>',
                $renderedItems
            );
            $this->assertStringContainsString(
                '<title>' . $media->title . '</title>',
                $renderedItems
            );
            $this->assertStringContainsString(
                '<enclosure url="' .
                    $media->enclosureUrl() .
                    '" length="' .
                    $media->length .
                    '" type="audio/mpeg" />',
                $renderedItems
            );
            $this->assertStringContainsString(
                '<pubDate>' . $media->pubDate() . '</pubDate>',
                $renderedItems
            );

            $this->assertStringContainsString(
                '<itunes:duration>' . $media->duration() . '</itunes:duration>',
                $renderedItems
            );
            $this->assertStringContainsString(
                '<itunes:explicit>' .
                    $media->channel->explicit() .
                    '</itunes:explicit>',
                $renderedItems
            );
        }
    }

    public function testWithOneMedia()
    {
        $media = factory(Media::class)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => $this->faker->dateTimeBetween(Carbon::parse('1 year ago'), Carbon::now())
        ]);
        $renderedItems = PodcastItems::prepare($this->channel)->render();
        $this->assertStringContainsString(
            '<guid>' . $media->media_id . '</guid>',
            $renderedItems
        );
        $this->assertStringContainsString(
            '<title>' . $media->title . '</title>',
            $renderedItems
        );
        $this->assertStringContainsString(
            '<enclosure url="' .
                $media->enclosureUrl() .
                '" length="' .
                $media->length .
                '" type="audio/mpeg" />',
            $renderedItems
        );
        $this->assertStringContainsString(
            '<pubDate>' . $media->pubDate() . '</pubDate>',
            $renderedItems
        );

        $this->assertStringContainsString(
            '<itunes:duration>' . $media->duration() . '</itunes:duration>',
            $renderedItems
        );
        $this->assertStringContainsString(
            '<itunes:explicit>' .
                $media->channel->explicit() .
                '</itunes:explicit>',
            $renderedItems
        );
    }
}
