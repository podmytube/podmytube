<?php

namespace Tests\Unit;

use App\Media;
use App\Channel;
use App\Podcast\PodcastItems;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastItemsTest extends TestCase
{
    use RefreshDatabase;

    protected static $channel;
    protected static $medias;
    protected static $dbIsWarm = false;

    protected static function warmDb()
    {
        self::$channel = factory(Channel::class)->create();
        self::$medias = factory(Media::class,3)->create(['channel_id' => self::$channel->channel_id]);
        self::$dbIsWarm = true;
    }

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    public function testExample()
    {
        $renderedItems = PodcastItems::prepare(self::$channel)->render();
        var_dump($renderedItems);

    }
}
