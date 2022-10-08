<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Channel;
use App\Models\Thumb;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @internal
 *
 * @coversNothing
 */
class FixRestoreThumbsCommandTest extends CommandTestCase
{
    use RefreshDatabase;

    public const JEAN_VIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';
    public const EXISTING_THUMB = 'VAwfQS2W5yJ0blTSVZWcbQqioA7qzGArn7RcLt4K.jpg';

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Filter _vig.* before');
    }

    /** @test */
    public function channel_covered_with_file_should_stay_unchanged(): void
    {
        $channel = Channel::factory()->create(['channel_id' => static::JEAN_VIET_CHANNEL_ID]);
        Thumb::factory()->channel($channel)->create(['file_name' => static::EXISTING_THUMB]);

        $this->assertNotNull($channel->cover);
        $this->assertEquals(static::EXISTING_THUMB, $channel->cover->file_name);

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);

        $channel->refresh();
        $this->assertNotNull($channel->cover);
        $this->assertEquals(static::EXISTING_THUMB, $channel->cover->file_name);
    }

    /** @test */
    public function channel_covered_with_no_files_in_should_have_no_cover(): void
    {
        $channel = Channel::factory()->create(['channel_id' => 'CHANNEL_WITH_NO_COVER']);
        Thumb::factory()->channel($channel)->create();

        $this->assertNotNull($channel->cover);

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);

        $channel->refresh();
        $this->assertNull($channel->cover);
    }

    /** @test */
    public function channel_covered_with_missing_file_should_be_updated_with_most_recent(): void
    {
        $channel = Channel::factory()->create(['channel_id' => static::JEAN_VIET_CHANNEL_ID]);
        Thumb::factory()->channel($channel)->create(['file_name' => 'this-file-do-not-exist.jpg']);

        $this->assertNotNull($channel->cover);
        $this->assertEquals('this-file-do-not-exist.jpg', $channel->cover->file_name);

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);
        $channel->refresh();

        $this->assertNotNull($channel->cover);
        $this->assertInstanceOf(Thumb::class, $channel->cover);
        $this->assertEquals(static::EXISTING_THUMB, $channel->cover->file_name);
    }

    /** @test */
    public function channel_uncovered_with_files_in_should_be_updated_with_most_recent(): void
    {
        $channel = Channel::factory()->create(['channel_id' => static::JEAN_VIET_CHANNEL_ID]);

        $this->assertNull($channel->cover);

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);
        $channel->refresh();

        $this->assertNotNull($channel->cover);
        $this->assertInstanceOf(Thumb::class, $channel->cover);
        $this->assertEquals(static::EXISTING_THUMB, $channel->cover->file_name);
    }

    /** @test */
    public function channel_uncovered_with_no_files_in_should_stay_unchanged(): void
    {
        $channel = Channel::factory()
            ->state([
                'channel_name' => 'jean viet',
                'channel_id' => 'CHANNEL_WITH_NO_COVER',
            ])
            ->create()
        ;

        $this->assertNull($channel->cover);

        $this->artisan('fix:restore-thumbs')->assertExitCode(0);

        $channel->refresh();
        $this->assertNull($channel->cover);
    }
}
