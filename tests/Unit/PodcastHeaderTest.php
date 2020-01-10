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

    protected static $thumb;
    protected static $channel;
    protected static $dbIsWarm = false;

    protected static function warmDb()
    {
        self::$channel = factory(Channel::class)->create();
        self::$thumb = factory(Thumb::class)->create(['channel_id' => self::$channel->channel_id]);
        self::$dbIsWarm = true;
    }

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    public function testingHeaderRenderingShouldBeFine()
    {
        $renderedResult = PodcastHeader::from(self::$channel)->render();
        $this->assertStringContainsString("<link>" . self::$channel->link . "</link>", $renderedResult);
        $this->assertStringContainsString("<title>" . self::$channel->title() . "</title>", $renderedResult);
        $this->assertStringContainsString("<description><![CDATA[" . self::$channel->description . "]]></description>", $renderedResult);
        $this->assertStringContainsString("<copyright>" . self::$channel->podcast_copyright . "</copyright>", $renderedResult);
        $this->assertStringContainsString("<language>" . self::$channel->lang . "</language>", $renderedResult);

        $this->assertStringContainsString("<image>", $renderedResult);
        $this->assertStringContainsString("<url>" . self::$channel->thumb->podcastUrl() . "</url>", $renderedResult);
        $this->assertStringContainsString("<title>" . self::$channel->title() . "</title>", $renderedResult);
        $this->assertStringContainsString("<link>" . self::$channel->link . "</link>", $renderedResult);
        $this->assertStringContainsString("</image>", $renderedResult);
        
        /**
         * Following part is test exhaustively into separate tests so here are the minimal part.
         */
        $this->assertStringContainsString("<itunes:author>".self::$channel->authors."</itunes:author>", $renderedResult);
        $this->assertStringContainsString("<itunes:title>".self::$channel->title()."</itunes:title>", $renderedResult);
        $this->assertStringContainsString("<itunes:owner>", $renderedResult);
        $this->assertStringContainsString("<itunes:name>".self::$channel->authors."</itunes:name>", $renderedResult);
        $this->assertStringContainsString("<itunes:email>".self::$channel->email."</itunes:email>", $renderedResult);
        $this->assertStringContainsString("</itunes:owner>", $renderedResult);
        $this->assertStringContainsString("<itunes:explicit>", $renderedResult);
        $this->assertStringContainsString("<itunes:category text=\"".trans('categories.'.self::$channel->category->name())."\" />", $renderedResult);        
    }
}
