<?php

namespace Tests\Unit\Podcast;

use App\Media;
use App\Thumb;
use App\Channel;
use App\Podcast\PodcastBuilder;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;

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
        self::$medias = factory(Media::class, 150)->create([
            'channel_id' => self::$channel->channel_id,
        ]);
        factory(Thumb::class)->create([
            'channel_id' => self::$channel->channel_id,
        ]);
    }

    public function testingFromStorageRelativePath()
    {
        $podcastBuilderObj = PodcastBuilder::prepare(self::$channel);
        $this->assertEquals(
            storage_path('app/public/feeds') .
                DIRECTORY_SEPARATOR .
                $podcastBuilderObj->relativePath(),
            $podcastBuilderObj->path()
        );
    }

    /**
     * Laravel is encoding.
     * So i'm encoding the same way to be sure tests will stay green.
     * By example "d'angelo" => "d&#039angelo";
     */
    public function stringEncodingLikeLaravel(string $str)
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML401);
    }

    public function testRenderingWholePodcast()
    {
        $renderedPodcast = ($podcastBuilder = PodcastBuilder::prepare(
            self::$channel
        ))->render();

        $this->assertStringContainsString(
            '<link>' . self::$channel->link . '</link>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<title>' . self::$channel->title() . '</title>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<description><![CDATA[' .
                self::$channel->description .
                ']]></description>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<copyright>' . self::$channel->podcast_copyright . '</copyright>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<language>' . self::$channel->lang . '</language>',
            $renderedPodcast
        );

        $this->assertStringContainsString('<image>', $renderedPodcast);
        $this->assertStringContainsString(
            '<url>' . self::$channel->thumb->podcastUrl() . '</url>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<title>' . self::$channel->title() . '</title>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<link>' . self::$channel->link . '</link>',
            $renderedPodcast
        );
        $this->assertStringContainsString('</image>', $renderedPodcast);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */

        $this->assertStringContainsString(
            '<itunes:author>' .
                $this->stringEncodingLikeLaravel(self::$channel->authors) .
                '</itunes:author>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:title>' . self::$channel->title() . '</itunes:title>',
            $renderedPodcast
        );
        $this->assertStringContainsString('<itunes:owner>', $renderedPodcast);
        $this->assertStringContainsString(
            '<itunes:name>' .
                $this->stringEncodingLikeLaravel(self::$channel->authors) .
                '</itunes:name>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:email>' . self::$channel->email . '</itunes:email>',
            $renderedPodcast
        );
        $this->assertStringContainsString('</itunes:owner>', $renderedPodcast);
        $this->assertStringContainsString(
            '<itunes:explicit>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            "<itunes:category text=\"" .
                self::$channel->category->categoryFeedValue() .
                "\" />",
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:image href="' .
                self::$channel->thumb->podcastUrl() .
                '" />',
            $renderedPodcast
        );

        /**
         * there should have some items too
         */
        $this->assertStringContainsString('<item>', $renderedPodcast);
        foreach (self::$medias as $media) {
            $this->assertStringContainsString(
                '<guid>' . $media->media_id . '</guid>',
                $renderedPodcast
            );
            $this->assertStringContainsString(
                '<title>' . $media->title . '</title>',
                $renderedPodcast
            );
            $this->assertStringContainsString(
                "<enclosure url=\"" .
                    $media->enclosureUrl() .
                    "\" length=\"" .
                    $media->length .
                    "\" type=\"audio/mpeg\" />",
                $renderedPodcast
            );
            $this->assertStringContainsString(
                '<pubDate>' . $media->pubDate() . '</pubDate>',
                $renderedPodcast
            );

            $this->assertStringContainsString(
                '<itunes:duration>' . $media->duration() . '</itunes:duration>',
                $renderedPodcast
            );
            $this->assertStringContainsString(
                '<itunes:explicit>' .
                    $media->channel->explicit() .
                    '</itunes:explicit>',
                $renderedPodcast
            );
        }
        $this->assertStringContainsString('</item>', $renderedPodcast);

        return $podcastBuilder;
    }

    /**
     * @depends testRenderingWholePodcast
     */
    public function testSavingIt($podcastBuilder)
    {
        $podcastBuilder->save();
        $this->assertTrue($podcastBuilder->exists());
        return $podcastBuilder;
    }

    /**
     * @depends testRenderingWholePodcast
     */
    public function testUrl($podcastBuilder)
    {
        $this->assertEquals(
            Storage::disk(PodcastBuilder::LOCAL_FEED_DISK)->url(
                $podcastBuilder->channel()->channelId() .
                    DIRECTORY_SEPARATOR .
                    PodcastBuilder::FEED_FILENAME
            ),
            $podcastBuilder->url()
        );
    }

    public function testProducingPodcastIsFine()
    {
        ($podcastBuilder = PodcastBuilder::prepare(self::$channel))->save();

        $savedPodcastContent = file_get_contents($podcastBuilder->path());

        $this->assertStringContainsString(
            '<link>' . self::$channel->link . '</link>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<title>' . self::$channel->title() . '</title>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<description><![CDATA[' .
                self::$channel->description .
                ']]></description>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<copyright>' . self::$channel->podcast_copyright . '</copyright>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<language>' . self::$channel->lang . '</language>',
            $savedPodcastContent
        );

        $this->assertStringContainsString('<image>', $savedPodcastContent);
        $this->assertStringContainsString(
            '<url>' . self::$channel->thumb->podcastUrl() . '</url>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<title>' . self::$channel->title() . '</title>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<link>' . self::$channel->link . '</link>',
            $savedPodcastContent
        );
        $this->assertStringContainsString('</image>', $savedPodcastContent);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString(
            '<itunes:author>' .
                $this->stringEncodingLikeLaravel(self::$channel->authors) .
                '</itunes:author>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:title>' . self::$channel->title() . '</itunes:title>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:owner>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:name>' .
                $this->stringEncodingLikeLaravel(self::$channel->authors) .
                '</itunes:name>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:email>' . self::$channel->email . '</itunes:email>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '</itunes:owner>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:explicit>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            "<itunes:category text=\"" .
                self::$channel->category->categoryFeedValue() .
                "\" />",
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:image href="' .
                self::$channel->thumb->podcastUrl() .
                '" />',
            $savedPodcastContent
        );

        /**
         * there should have some items too
         */
        $this->assertStringContainsString('<item>', $savedPodcastContent);
        foreach (self::$medias as $media) {
            $this->assertStringContainsString(
                '<guid>' . $media->media_id . '</guid>',
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                '<title>' . $media->title . '</title>',
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                "<enclosure url=\"" .
                    $media->enclosureUrl() .
                    "\" length=\"" .
                    $media->length .
                    "\" type=\"audio/mpeg\" />",
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                '<pubDate>' . $media->pubDate() . '</pubDate>',
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                '<itunes:duration>' . $media->duration() . '</itunes:duration>',
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                '<itunes:explicit>' .
                    $media->channel->explicit() .
                    '</itunes:explicit>',
                $savedPodcastContent
            );
        }
        $this->assertStringContainsString('</item>', $savedPodcastContent);
    }
}
