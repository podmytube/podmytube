<?php

namespace Tests\Unit\Podcast;

use App\Thumb;
use App\Channel;
use App\Podcast\PodcastHeader;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastHeaderTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create();
    }

    public function testingNewChannelWithNoThumbShouldRenderFine()
    {
        $renderedResult = ($podcastHeaderObj = PodcastHeader::init()->forChannel(
            $this->channel
        ))->render();

        $this->assertEquals(
            Thumb::defaultUrl(),
            $podcastHeaderObj->podcastCover()->url()
        );

        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<description><![CDATA[' .
                $this->channel->description .
                ']]></description>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<copyright>' . $this->channel->podcast_copyright . '</copyright>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<language>' . $this->channel->language->code . '</language>',
            $renderedResult
        );

        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString(
            '<url>' . Thumb::defaultUrl() . '</url>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $renderedResult
        );
        $this->assertStringContainsString('</image>', $renderedResult);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString(
            '<itunes:author>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:author>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:title>' . $this->channel->title() . '</itunes:title>',
            $renderedResult
        );
        $this->assertStringContainsString('<itunes:owner>', $renderedResult);
        $this->assertStringContainsString(
            '<itunes:name>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:name>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:email>' . $this->channel->email . '</itunes:email>',
            $renderedResult
        );
        $this->assertStringContainsString('</itunes:owner>', $renderedResult);
        $this->assertStringContainsString('<itunes:explicit>', $renderedResult);
        $this->assertStringContainsString(
            '<itunes:category text="' .
                $this->channel->category->feedValue() .
                '" />',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:image href="' . Thumb::defaultUrl() . '" />',
            $renderedResult
        );
    }

    public function testingHeaderRenderingShouldBeFine()
    {
        factory(Thumb::class)->create([
            'channel_id' => $this->channel->channel_id,
        ]);

        $renderedResult = PodcastHeader::init()->forChannel($this->channel)->render();
        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<description><![CDATA[' .
                $this->channel->description .
                ']]></description>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<copyright>' . $this->channel->podcast_copyright . '</copyright>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<language>' . $this->channel->language->code . '</language>',
            $renderedResult
        );

        $this->assertStringContainsString('<image>', $renderedResult);
        $this->assertStringContainsString(
            '<url>' . $this->channel->thumb->podcastUrl() . '</url>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<title>' . $this->channel->title() . '</title>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<link>' . $this->channel->link . '</link>',
            $renderedResult
        );
        $this->assertStringContainsString('</image>', $renderedResult);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString(
            '<itunes:author>' . $this->stringEncodingLikeLaravel($this->channel->authors) . '</itunes:author>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:title>' . $this->channel->title() . '</itunes:title>',
            $renderedResult
        );
        $this->assertStringContainsString('<itunes:owner>', $renderedResult);
        $this->assertStringContainsString(
            '<itunes:name>' .
                $this->stringEncodingLikeLaravel($this->channel->authors) .
                '</itunes:name>',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:email>' . $this->channel->email . '</itunes:email>',
            $renderedResult
        );
        $this->assertStringContainsString('</itunes:owner>', $renderedResult);
        $this->assertStringContainsString('<itunes:explicit>', $renderedResult);
        $this->assertStringContainsString(
            '<itunes:category text="' .
                $this->channel->category->feedValue() .
                '" />',
            $renderedResult
        );
        $this->assertStringContainsString(
            '<itunes:image href="' .
                $this->channel->thumb->podcastUrl() .
                '" />',
            $renderedResult
        );
    }
}
