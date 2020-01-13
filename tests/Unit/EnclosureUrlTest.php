<?php

/**
 * This will test the enclosure url class.
 *
 * @category Test
 * @package  PodMyTube\classic
 * @author   Frederick Tyteca <fred@podmytube.com>
 * @license  http://www.podmytube.com closed
 * @link     Podmytube website, http://www.podmytube.com
 */

use App\Media;
use App\Modules\EnclosureUrl;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class EnclosureUrlTest
 *
 * @category Podmytube
 * @package  Podmytube
 * @author   Frederik Tyteca <frederick@podmytube.com>
 */

class EnclosureUrlTest extends TestCase
{
    use RefreshDatabase;

    protected static $media;
    protected static $dbIsWarm = false;

    protected static function warmDb()
    {
        self::$media = factory(Media::class)->create()->first();
        self::$dbIsWarm = true;
    }

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    public function testingEnclosureUrlIsValid()
    {
        $this->assertEquals(
            getenv('MP3_URL') . '/'.self::$media->channel_id.'/'.self::$media->media_id.'.mp3',
            EnclosureUrl::create(self::$media)->get()
        );
    }
};
