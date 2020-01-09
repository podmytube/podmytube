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
    /** used to remove every created data in database */
    use RefreshDatabase;

    protected static $thumb;
    protected static $channel;
    protected static $dbIsWarm = false;

    protected static function warmDb()
    {
        self::$channel = factory(Channel::class)->create();
        //self::$thumb = factory(Thumb::class)->create(['channel_id' => self::$channel->channelId()]);
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
    }
}
