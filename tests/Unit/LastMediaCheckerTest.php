<?php

namespace Tests\Unit;

use App\Channel;
use App\Modules\LastMediaChecker;
use App\Plan;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * the main parts of these tests is based on my personal youtube channel
 * the last media has been published on Oct 28, 2015
 */
class LastMediaCheckerTest extends TestCase
{
    use RefreshDatabase;

    public const DELAY_IN_HOURS = 6;
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    public function testingMediaHasBeenPublishedRecentlyShouldBeOk()
    {
        $channel = factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        $this->assertFalse(
            LastMediaChecker::for($channel)->mediaHasBeenPublishedRecently(),
            "Last video on my personnal channel has been published long ago."
        );
    }

    public function testMediaHasBeenGrabbedShouldBeGood()
    {
        $channel = factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        $this->assertFalse(
            LastMediaChecker::for($channel)->isTheMediaGrabbed(),
            "Last video on my personnal channel has never been grabbed."
        );
    }

    public function testMediaIsNotExcludedByAnyFilter()
    {
        $channel = factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);
        $this->assertFalse(
            LastMediaChecker::for($channel)->mediaIsExcludedByTag(),
            "This channel does not reject any video (no filters) last media should not be excluded"
        );
    }

    public function testMediaIsTooOldForThisChannel()
    {
        /** 
         * will reject all videos published before yesterday 
         * mine has been published in 2015 => rejection
         */
        $channel = factory(Channel::class)->create([
            'channel_id' => self::PERSONAL_CHANNEL_ID,
            'reject_video_too_old' => Carbon::parse("yesterday")
        ]);
        $this->assertTrue(
            LastMediaChecker::for($channel)->isMediaExcludedByDate(),
            "This channel is rejecting videos before yesterday. This one should be rejected too."
        );
    }

    public function testMediaIsRecentEnoughForThisChannel()
    {
        /** channel is accepting videos since 2009 */
        $channel = factory(Channel::class)->create([
            'channel_id' => self::PERSONAL_CHANNEL_ID,
            'reject_video_too_old' => Carbon::parse("first day of 2009")
        ]);
        $this->assertFalse(
            LastMediaChecker::for($channel)->isMediaExcludedByDate(),
            "This channel is rejecting videos before 2009. This one should be accepted."
        );
    }

    public function testChannelDoesNotCareAboutPublishedDate()
    {
        /** channel does not care about old videos, all accepted */
        $channel = factory(Channel::class)->create([
            'channel_id' => self::PERSONAL_CHANNEL_ID,
        ]);
        $this->assertFalse(
            LastMediaChecker::for($channel)->isMediaExcludedByDate(),
            "This channel is not rejecting any videos by date. This one should be accepted too."
        );
    }

    public function testMediaIsAcceptingOnlyAnimalsTag()
    {
        $channel = factory(Channel::class)->create([
            'channel_id' => self::PERSONAL_CHANNEL_ID,
            'accept_video_by_tag' => 'poney, cat',
        ]);
        $this->assertTrue(
            LastMediaChecker::for($channel)->mediaIsExcludedByTag(),
            "This channel is accepting only animals tag. Mine is not tagged with animals and should be excluded "
        );
    }

    public function testMediaShouldBeGrabbed()
    {
        $this->markTestIncomplete("to be done");

        factory(Subscription::class)->create(
            [
                'channel_id' => $this->channel->channel_id,
                'plan_id' => Plan::bySlug('weekly_youtuber'),
            ]
        );
        LastMediaChecker::for($this->channel)->shouldMediaBeingGrabbed();
    }

    public function testMediaShouldHaveBeenGrabbed()
    {
        $this->markTestIncomplete("to be done");
    }
}
