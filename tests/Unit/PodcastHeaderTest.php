<?php

namespace Tests\Unit;

use App\Thumb;
use App\Channel;
use App\Podcast\PodcastHeader;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastHeaderTest extends TestCase
{
    /** used to remove every created data in database */
    use DatabaseTransactions;

    protected static $thumb;
    protected static $channel;
    protected static $dbIsWarm = false;

    protected static function warmDb()
    {
        self::$channel = factory(Channel::class)->create();
        self::$thumb = factory(Thumb::class)->create(['channel_id' => self::$channel->channelId()]);
        self::$dbIsWarm = true;
    }

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    public function testfoo()
    {
        var_dump(self::$thumb);
        $this->assertTrue(false);
    }
    /* public function testingNoInformationsShouldRenderNothing()
    {
        $renderedResult = PodcastHeader::prepare()->render();
        var_dump($renderedResult);die("\e[30;48;5;166m".__FILE__ . '::' . __LINE__ ."\e[0m". PHP_EOL);
        $this->assertEmpty($renderedResult);
    } */
}
