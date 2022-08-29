<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Channel;
use App\Models\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FixRestoreThumbsCommandTest extends TestCase
{
    use RefreshDatabase;

    public const JEAN_VIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';
    public const EXISTING_THUMB = 'VAwfQS2W5yJ0blTSVZWcbQqioA7qzGArn7RcLt4K.jpg';

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete('TO BE DONE');
    }

    /** @test */
    public function channel_having_thumb_and_file_should_stay_unchanged(): void
    {
        $thumb = Thumb::factory()
            ->state(['file_name' => static::EXISTING_THUMB])
            ->for(
                Channel::factory()
                    ->state([
                        'channel_name' => 'jean viet',
                        'channel_id' => static::JEAN_VIET_CHANNEL_ID,
                    ]),
                'coverable'
            )
            ->create()
        ;

        $this->assertEquals(static::EXISTING_THUMB, $thumb->file_name);
        $this->assertNotNull($thumb->coverable);
        $this->assertInstanceOf(Channel::class, $thumb->coverable);
        $this->assertEquals(
            Channel::byChannelId(static::JEAN_VIET_CHANNEL_ID)->podcast_title,
            $thumb->coverable->podcast_title
        );
        $this->artisan('fix:restore-thumbs')->assertExitCode(0);

        $thumb->refresh();
        $this->assertEquals(static::EXISTING_THUMB, $thumb->file_name);
        $this->assertNotNull($thumb->coverable);
        $this->assertInstanceOf(Channel::class, $thumb->coverable);
        $this->assertEquals(
            Channel::byChannelId(static::JEAN_VIET_CHANNEL_ID)->podcast_title,
            $thumb->coverable->podcast_title
        );
    }

    /** @test */
    public function channel_having_thumb_without_file_should_be_updated(): void
    {
        $thumb = Thumb::factory()
            ->state(['file_name' => 'this-file-do-not-exist.jpg'])
            ->for(
                Channel::factory()
                    ->state([
                        'channel_name' => 'jean viet',
                        'channel_id' => static::JEAN_VIET_CHANNEL_ID,
                    ]),
                'coverable'
            )
            ->create()
        ;

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);
        $this->assertEquals(static::EXISTING_THUMB, $thumb->file_name);
        $this->assertNotNull($thumb->coverable);
        $this->assertInstanceOf(Channel::class, $thumb->coverable);
        $this->assertEquals(
            Channel::byChannelId(static::JEAN_VIET_CHANNEL_ID)->podcast_title,
            $thumb->coverable->podcast_title
        );
    }

    /** @test */
    public function channel_having_no_thumb_but_with_file_should_be_updated(): void
    {
        $channel = Channel::factory()
            ->state([
                'channel_name' => 'jean viet',
                'channel_id' => static::JEAN_VIET_CHANNEL_ID,
            ])
            ->create()
        ;

        $this->assertNull($channel->cover);

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);
        $channel->refresh();

        $this->assertNotNull($channel->cover);
        $this->assertInstanceOf(Thumb::class, $channel->cover);
    }
}
