<?php

namespace Tests\Unit\Podcast;

use App\Media;
use App\Thumb;
use App\Channel;
use App\Podcast\PodcastBuilder;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Storage;

class PodcastBuilderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $channel;
    protected $medias;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
        $this->medias = factory(Media::class, 50)->create([
            'channel_id' => $this->channel->channel_id,
            'grabbed_at' => $this->faker->dateTimeBetween(Carbon::parse('1 year ago'), Carbon::now())
        ]);
        factory(Thumb::class)->create([
            'channel_id' => $this->channel->channel_id,
        ]);
    }

    public function testingFromStorageRelativePath()
    {
        $expectedFeedPath = storage_path('app/public/feeds') .
            '/' .
            $this->channel->id() .
            '/' .
            PodcastBuilder::FEED_FILENAME;
        $podcastBuilderObj = PodcastBuilder::forChannel($this->channel);
        $this->assertEquals(
            $expectedFeedPath,
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
        $podcastBuilder = PodcastBuilder::forChannel($this->channel)->build();
        $renderedPodcast = $podcastBuilder->render();

        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<description><![CDATA[' .
                $this->channel->description .
                ']]></description>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<copyright>' . $this->channel->podcast_copyright . '</copyright>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<language>' . $this->channel->lang . '</language>',
            $renderedPodcast
        );

        $this->assertStringContainsString('<image>', $renderedPodcast);
        $this->assertStringContainsString(
            '<url>' . $this->channel->thumb->podcastUrl() . '</url>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $renderedPodcast
        );
        $this->assertStringContainsString('</image>', $renderedPodcast);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */

        $this->assertStringContainsString(
            '<itunes:author>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:author>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:title>' . $this->channel->title() . '</itunes:title>',
            $renderedPodcast
        );
        $this->assertStringContainsString('<itunes:owner>', $renderedPodcast);
        $this->assertStringContainsString(
            '<itunes:name>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:name>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:email>' . $this->channel->email . '</itunes:email>',
            $renderedPodcast
        );
        $this->assertStringContainsString('</itunes:owner>', $renderedPodcast);
        $this->assertStringContainsString(
            '<itunes:explicit>',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:category text="' .
                $this->channel->category->categoryFeedValue() .
                '" />',
            $renderedPodcast
        );
        $this->assertStringContainsString(
            '<itunes:image href="' .
                $this->channel->thumb->podcastUrl() .
                '" />',
            $renderedPodcast
        );

        /**
         * there should have some items too
         */
        $this->assertStringContainsString('<item>', $renderedPodcast);
        foreach ($this->medias as $media) {
            $this->assertStringContainsString(
                '<guid>' . $media->media_id . '</guid>',
                $renderedPodcast
            );
            $this->assertStringContainsString(
                '<title>' . $media->title . '</title>',
                $renderedPodcast
            );
            $this->assertStringContainsString(
                '<enclosure url="' .
                    $media->enclosureUrl() .
                    '" length="' .
                    $media->length .
                    '" type="audio/mpeg" />',
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
        $podcastBuilder = PodcastBuilder::forChannel($this->channel)->build();
        $podcastBuilder->save();

        $savedPodcastContent = file_get_contents($podcastBuilder->path());

        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<description><![CDATA[' .
                $this->channel->description .
                ']]></description>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<copyright>' . $this->channel->podcast_copyright . '</copyright>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<language>' . $this->channel->lang . '</language>',
            $savedPodcastContent
        );

        $this->assertStringContainsString('<image>', $savedPodcastContent);
        $this->assertStringContainsString(
            '<url>' . $this->channel->thumb->podcastUrl() . '</url>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $savedPodcastContent
        );
        $this->assertStringContainsString('</image>', $savedPodcastContent);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString(
            '<itunes:author>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:author>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:title>' . $this->channel->title() . '</itunes:title>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:owner>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:name>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:name>',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:email>' . $this->channel->email . '</itunes:email>',
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
            '<itunes:category text="' .
                $this->channel->category->categoryFeedValue() .
                '" />',
            $savedPodcastContent
        );
        $this->assertStringContainsString(
            '<itunes:image href="' .
                $this->channel->thumb->podcastUrl() .
                '" />',
            $savedPodcastContent
        );

        /**
         * there should have some items too
         */
        $this->assertStringContainsString('<item>', $savedPodcastContent);
        foreach ($this->medias as $media) {
            $this->assertStringContainsString(
                '<guid>' . $media->media_id . '</guid>',
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                '<title>' . $media->title . '</title>',
                $savedPodcastContent
            );
            $this->assertStringContainsString(
                '<enclosure url="' .
                    $media->enclosureUrl() .
                    '" length="' .
                    $media->length .
                    '" type="audio/mpeg" />',
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
