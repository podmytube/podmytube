<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Channel;
use App\Media;
use App\Modules\LastMediaChecker;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * the main parts of these tests is based on my personal youtube channel
 * the last media has been published on Oct 28, 2015.
 *
 * @internal
 * @coversNothing
 */
class LastMediaCheckerTest extends TestCase
{
    use RefreshDatabase;

    public const DELAY_IN_HOURS = 6;

    /** @var \App\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->channel = Channel::factory()->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
    }

    /** @test */
    public function has_been_published_recently_should_be_false(): void
    {
        $this->assertFalse(
            LastMediaChecker::forChannel($this->channel)->run()->hasMediaBeenPublishedRecently(),
            'Last video on my personnal channel has been on 28/10/2015. '
        );
    }

    /** @test */
    public function media_published_long_ago_is_still_unknown_should_have_been_grabbed(): void
    {
        $this->channel->update([
            'accept_video_by_tag' => 'foolish, stupid, awesome', // last video of my channel has tags dev,podmytube
            'reject_video_too_old' => Carbon::create(2015, 10, 1), // my last video has been published on 28/10/2015
        ]);

        $this->assertTrue(
            LastMediaChecker::forChannel($this->channel)->run()->shouldMediaBeingGrabbed(),
            'My last video have none of this tag. Unknown in db => alert.'
        );
    }

    /** @test */
    public function media_published_long_ago_should_have_been_grabbed(): void
    {
        $this->channel->update([
            'accept_video_by_tag' => 'foolish, stupid, awesome', // last video of my channel has tags dev,podmytube
            'reject_video_too_old' => Carbon::create(2015, 10, 1), // my last video has been published on 28/10/2015
        ]);

        // creating media
        Media::factory()->create([
            'media_id' => self::BEACH_VOLLEY_VIDEO_1,
            'channel_id' => $this->channel->channel_id,
            'published_at' => Carbon::createFromDate(2015, 10, 28), ]);

        $this->assertFalse(
            LastMediaChecker::forChannel($this->channel)->run()->shouldMediaBeingGrabbed(),
            'My last video have none of this tag. This media should not be grabbed. No alert.'
        );
    }

    /** @test */
    public function media_published_long_ago_has_already_been_grabbed(): void
    {
        $this->channel->update([
            'accept_video_by_tag' => 'foolish, stupid, awesome', // last video of my channel has tags dev,podmytube
            'reject_video_too_old' => Carbon::create(2015, 10, 1), // my last video has been published on 28/10/2015
        ]);

        // creating media
        Media::factory()
            ->grabbedAt(now())
            ->create([
            'media_id' => self::BEACH_VOLLEY_VIDEO_1,
            'channel_id' => $this->channel->channel_id,
        ]);

        $this->assertFalse(
            LastMediaChecker::forChannel($this->channel)->run()->shouldMediaBeingGrabbed(),
            'Media is grabbed. => no alert'
        );
    }
}
