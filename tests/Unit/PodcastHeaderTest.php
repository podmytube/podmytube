<?php

namespace Tests\Unit;

use App\Thumb;
use App\Channel;
use App\Podcast\PodcastHeader;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function testingNewChannelWithNoThumbShouldRenderFine()
    {
        $channel = factory(Channel::class)->create();

        $renderedResult = ($podcastHeaderObj = PodcastHeader::generateFor($channel))->render();

        $this->assertEquals(
            Thumb::defaultUrl(),
            $podcastHeaderObj->podcastCover()->url()
        );
        
        $this->assertStringContainsString("<link>" . $channel->link . "</link>", $renderedResult);
        $this->assertStringContainsString("<title>" . $channel->title() . "</title>", $renderedResult);
        $this->assertStringContainsString("<description><![CDATA[" . $channel->description . "]]></description>", $renderedResult);
        $this->assertStringContainsString("<copyright>" . $channel->podcast_copyright . "</copyright>", $renderedResult);
        $this->assertStringContainsString("<language>" . $channel->lang . "</language>", $renderedResult);

        $this->assertStringContainsString("<image>", $renderedResult);
        $this->assertStringContainsString("<url>" . Thumb::defaultUrl() . "</url>", $renderedResult);
        $this->assertStringContainsString("<title>" . $channel->title() . "</title>", $renderedResult);
        $this->assertStringContainsString("<link>" . $channel->link . "</link>", $renderedResult);
        $this->assertStringContainsString("</image>", $renderedResult);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString("<itunes:author>" . $channel->authors . "</itunes:author>", $renderedResult);
        $this->assertStringContainsString("<itunes:title>" . $channel->title() . "</itunes:title>", $renderedResult);
        $this->assertStringContainsString("<itunes:owner>", $renderedResult);
        $this->assertStringContainsString("<itunes:name>" . $channel->authors . "</itunes:name>", $renderedResult);
        $this->assertStringContainsString("<itunes:email>" . $channel->email . "</itunes:email>", $renderedResult);
        $this->assertStringContainsString("</itunes:owner>", $renderedResult);
        $this->assertStringContainsString("<itunes:explicit>", $renderedResult);
        $this->assertStringContainsString("<itunes:category text=\"" . $channel->category->categoryFeedValue() . "\" />", $renderedResult);
    }

    public function testingHeaderRenderingShouldBeFine()
    {
        $channel = factory(Channel::class)->create();
        $thumb = factory(Thumb::class)->create(['channel_id' => $channel->channel_id]);
        
        $renderedResult = PodcastHeader::generateFor($channel)->render();
        $this->assertStringContainsString("<link>" . $channel->link . "</link>", $renderedResult);
        $this->assertStringContainsString("<title>" . $channel->title() . "</title>", $renderedResult);
        $this->assertStringContainsString("<description><![CDATA[" . $channel->description . "]]></description>", $renderedResult);
        $this->assertStringContainsString("<copyright>" . $channel->podcast_copyright . "</copyright>", $renderedResult);
        $this->assertStringContainsString("<language>" . $channel->lang . "</language>", $renderedResult);

        $this->assertStringContainsString("<image>", $renderedResult);
        $this->assertStringContainsString("<url>" . $channel->thumb->podcastUrl() . "</url>", $renderedResult);
        $this->assertStringContainsString("<title>" . $channel->title() . "</title>", $renderedResult);
        $this->assertStringContainsString("<link>" . $channel->link . "</link>", $renderedResult);
        $this->assertStringContainsString("</image>", $renderedResult);

        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString("<itunes:author>" . $channel->authors . "</itunes:author>", $renderedResult);
        $this->assertStringContainsString("<itunes:title>" . $channel->title() . "</itunes:title>", $renderedResult);
        $this->assertStringContainsString("<itunes:owner>", $renderedResult);
        $this->assertStringContainsString("<itunes:name>" . $channel->authors . "</itunes:name>", $renderedResult);
        $this->assertStringContainsString("<itunes:email>" . $channel->email . "</itunes:email>", $renderedResult);
        $this->assertStringContainsString("</itunes:owner>", $renderedResult);
        $this->assertStringContainsString("<itunes:explicit>", $renderedResult);
        $this->assertStringContainsString("<itunes:category text=\"" . $channel->category->categoryFeedValue() . "\" />", $renderedResult);
    }
}
