<?php

declare(strict_types=1);

/**
 * This will test the enclosure url class.
 *
 * @category Test
 *
 * @author   Frederick Tyteca <fred@podmytube.com>
 * @license  http://www.podmytube.com closed
 *
 * @see     Podmytube website, http://www.podmytube.com
 */

use App\Media;
use App\Modules\EnclosureUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class EnclosureUrlTest.
 *
 * @category Podmytube
 *
 * @author   Frederik Tyteca <frederick@podmytube.com>
 *
 * @internal
 * @coversNothing
 */
class EnclosureUrlTest extends TestCase
{
    use RefreshDatabase;

    protected static $media;
    protected static $dbIsWarm = false;

    public function setUp(): void
    {
        parent::setUp();
        if (!static::$dbIsWarm) {
            static::warmDb();
        }
    }

    /** @test */
    public function enclosure_url_is_valid(): void
    {
        $this->assertEquals(
            config('app.mp3_url') . '/' . self::$media->channel_id . '/' . self::$media->media_id . '.mp3',
            EnclosureUrl::create(self::$media)->get()
        );
    }

    protected static function warmDb(): void
    {
        self::$media = Media::factory()->create()->first();
        self::$dbIsWarm = true;
    }
}
