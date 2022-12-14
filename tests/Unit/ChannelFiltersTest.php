<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChannelFiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
    }

    public function test_has_accept_only_tag_is_ok(): void
    {
        $this->assertFalse($this->channel->hasAcceptOnlyTags());

        $this->channel->update(['accept_video_by_tag' => 'poney']);
        $this->assertTrue($this->channel->hasAcceptOnlyTags());

        $this->channel->update(['accept_video_by_tag' => 'poney, mouse,dog']);
        $this->assertTrue($this->channel->hasAcceptOnlyTags());
    }

    /** @test */
    public function is_tag_accepted_is_ok(): void
    {
        $this->assertTrue(
            $this->channel->isTagAccepted('podcast'),
            'Channel has no filtering, "podcast" should be accepted.'
        );
        $this->assertTrue(
            $this->channel->isTagAccepted(''),
            'Channel has no filtering, "" should be accepted.'
        );
        $this->assertTrue(
            $this->channel->isTagAccepted(' '),
            'Channel has no filtering, " " should be accepted.'
        );
        $this->assertTrue(
            $this->channel->isTagAccepted(),
            'Channel has no filtering, null should be accepted.'
        );

        // adding filtering on "podcast" only
        $this->channel->update(['accept_video_by_tag' => 'podcast']);
        $this->assertTrue(
            $this->channel->isTagAccepted('podcast'),
            'Channel is filtering on "podcast", "podcast" should be accepted.'
        );
        $this->assertFalse(
            $this->channel->isTagAccepted(),
            'Channel is filtering on "podcast", null should be rejected.'
        );
        $this->assertFalse(
            $this->channel->isTagAccepted(''),
            'Channel is filtering on "podcast", "" should be rejected.'
        );
        $this->assertFalse(
            $this->channel->isTagAccepted('window'),
            'Channel is filtering on "podcast", null should be rejected.'
        );
    }

    /** @test */
    public function is_tag_accepted_on_unwanted_characters_is_ok(): void
    {
        $this->assertTrue(
            $this->channel->isTagAccepted(' '),
            'Channel is not filtering, <space> should be accepted.'
        );

        $this->assertTrue(
            $this->channel->isTagAccepted("\n"),
            'Channel is not filtering, <newline> should be accepted.'
        );

        // with filtering
        $this->channel->update(['accept_video_by_tag' => 'podcast']);
        $this->assertFalse(
            $this->channel->isTagAccepted(' '),
            'Channel is filtering on "podcast", <space> should be accepted.'
        );

        $this->assertFalse(
            $this->channel->isTagAccepted("\n"),
            'Channel is filtering on "podcast", <newline> should be accepted.'
        );
    }

    /** @test */
    public function are_tags_accepted_is_ok(): void
    {
        // channel with no filter accept everything
        $this->assertTrue(
            $this->channel->areTagsAccepted(['window', 'house']),
            'Channel is filtering nothing, media with tags ["window", "house"] should be accepted.'
        );

        $this->assertTrue(
            $this->channel->areTagsAccepted([]),
            'Channel is filtering nothing, media with no tags should be accepted.'
        );

        $this->assertTrue(
            $this->channel->areTagsAccepted(),
            'Channel is filtering nothing, media with no tags should be accepted.'
        );

        // channel with some only tags to accept
        $this->channel->update(['accept_video_by_tag' => 'podcast']);

        $this->assertFalse(
            $this->channel->areTagsAccepted(),
            'Channel is filtering nothing, media with no tags should be accepted.'
        );

        $this->assertFalse(
            $this->channel->areTagsAccepted([]),
            'Channel is accepting only videos with podcast tag, video with no tag should be rejected.'
        );

        $this->assertFalse(
            $this->channel->areTagsAccepted(['not-the-one-accepted']),
            'Channel is accepting only videos with podcast tag, video with another tag should be rejected.'
        );

        // channel with some only tags to accept
        $this->channel->update(['accept_video_by_tag' => 'poney, cat, dog, chicken']);

        $this->assertTrue(
            $this->channel->areTagsAccepted(['cat', 'mouse']),
            'cat is one of the tags that channel is accepting so it should be accepted.'
        );
        $this->assertFalse(
            $this->channel->areTagsAccepted(['window', 'house']),
            'neither window nor house is in the list of allowed tags so it should be rejected.'
        );

        // filtering by date should change nothing
        $this->channel = Channel::factory()->create([
            'accept_video_by_tag' => 'poney, cat, dog, chicken',
            'reject_video_too_old' => Carbon::parse('10 years ago'),
        ]);
        $this->assertTrue($this->channel->areTagsAccepted(['cat', 'mouse']), 'cat is one of the tags that channel is accepting so it should be accepted.');
        $this->assertFalse($this->channel->areTagsAccepted(['window', 'house']), 'neither window nor house is in the list of allowed tags so it should be rejected.');
    }

    /** @test */
    public function is_date_accepted_is_ok(): void
    {
        /*
         * channel does not care about video too old
         * all should be accepted
         */
        $this->channel->update(['reject_video_too_old' => null]);
        $this->assertTrue(
            $this->channel->isDateAccepted(Carbon::parse('first day of 2009')),
            'Channel is accepting all videos even the old ones. This date should be accepted'
        );

        $this->channel->update(['reject_video_too_old' => now()]);
        $this->assertFalse(
            $this->channel->isDateAccepted(now()->subDay()),
            'Channel is accepting videos from now only. Yesterday should be rejected'
        );
    }
}
