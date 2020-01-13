<?php

namespace Tests\Unit;

use App\Media;
use App\Thumb;
use App\Channel;
use App\Podcast\PodcastBuilder;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected static $channel;
    protected static $medias;
    protected static $fileDestination;

    public function setUp(): void
    {
        parent::setUp();
        self::$channel = factory(Channel::class)->create();
        self::$medias = factory(Media::class, 150)->create(['channel_id' => self::$channel->channel_id]);
        factory(Thumb::class)->create(['channel_id' => self::$channel->channel_id]);
    }

    public function testRenderingWholePodcast()
    {
        $renderedPodcast = ($podcastBuilder = PodcastBuilder::prepare(self::$channel, self::$fileDestination))->render();

        $this->assertStringContainsString("<link>" . self::$channel->link . "</link>", $renderedPodcast);
        $this->assertStringContainsString("<title>" . self::$channel->title() . "</title>", $renderedPodcast);
        $this->assertStringContainsString("<description><![CDATA[" . self::$channel->description . "]]></description>", $renderedPodcast);
        $this->assertStringContainsString("<copyright>" . self::$channel->podcast_copyright . "</copyright>", $renderedPodcast);
        $this->assertStringContainsString("<language>" . self::$channel->lang . "</language>", $renderedPodcast);

        $this->assertStringContainsString("<image>", $renderedPodcast);
        $this->assertStringContainsString("<url>" . self::$channel->thumb->podcastUrl() . "</url>", $renderedPodcast);
        $this->assertStringContainsString("<title>" . self::$channel->title() . "</title>", $renderedPodcast);
        $this->assertStringContainsString("<link>" . self::$channel->link . "</link>", $renderedPodcast);
        $this->assertStringContainsString("</image>", $renderedPodcast);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString("<itunes:author>" . self::$channel->authors . "</itunes:author>", $renderedPodcast);
        $this->assertStringContainsString("<itunes:title>" . self::$channel->title() . "</itunes:title>", $renderedPodcast);
        $this->assertStringContainsString("<itunes:owner>", $renderedPodcast);
        $this->assertStringContainsString("<itunes:name>" . self::$channel->authors . "</itunes:name>", $renderedPodcast);
        $this->assertStringContainsString("<itunes:email>" . self::$channel->email . "</itunes:email>", $renderedPodcast);
        $this->assertStringContainsString("</itunes:owner>", $renderedPodcast);
        $this->assertStringContainsString("<itunes:explicit>", $renderedPodcast);
        $this->assertStringContainsString("<itunes:category text=\"" . trans('categories.' . self::$channel->category->name()) . "\" />", $renderedPodcast);

        /**
         * there should have some items too
         */
        $this->assertStringContainsString("<item>", $renderedPodcast);
        foreach (self::$medias as $media) {
            $this->assertStringContainsString("<guid>" . $media->media_id . "</guid>", $renderedPodcast);
            $this->assertStringContainsString("<title>" . $media->title . "</title>", $renderedPodcast);
            $this->assertStringContainsString("<enclosure url=\"" . $media->enclosureUrl() . "\" length=\"" . $media->length . "\" type=\"audio/mpeg\" />", $renderedPodcast);
            $this->assertStringContainsString("<pubDate>" . $media->pubDate() . "</pubDate>", $renderedPodcast);

            $this->assertStringContainsString("<itunes:duration>" . $media->duration() . "</itunes:duration>", $renderedPodcast);
            $this->assertStringContainsString("<itunes:explicit>" . $media->channel->explicit() . "</itunes:explicit>", $renderedPodcast);
        }
        $this->assertStringContainsString("</item>", $renderedPodcast);

        return $podcastBuilder;
    }

    /**
     * @depends testRenderingWholePodcast
     */
    public function testSavingIt($podcastBuilder)
    {
        $podcastBuilder->save();
        $this->assertTrue($podcastBuilder->exists());
    }


}
