<?php

namespace Tests\Unit;

use App\Channel;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function testingHasAcceptOnlyTag()
    {
        $channel = factory(Channel::class)->create([
            // accept_only_tags => 'poney, cat',
            'accept_video_by_tag' => 'poney, cat',
        ]);
        $this->assertTrue($channel->hasAcceptOnlyTags());

        $channel = factory(Channel::class)->create();
        $this->assertFalse($channel->hasAcceptOnlyTags());
    }

    public function testingIsTagInAcceptedOnlyTagsShouldBeGood()
    {
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'poney, cat',
        ]);
        $this->assertTrue($channel->isTagInAcceptedOnlyTags('cat'));
        $this->assertFalse($channel->isTagInAcceptedOnlyTags('window'));

        /** channel with no accepted tag is accepting all */
        $channel = factory(Channel::class)->create();
        $this->assertTrue($channel->isTagInAcceptedOnlyTags('window'));
    }

    public function testingIsTagAccepted()
    {
        /** channel with no filter accept everything */
        $channel = factory(Channel::class)->create();
        $this->assertTrue($channel->isTagAccepted('window'), 'channel is filtering nothing, tag should be accepted.');

        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'poney, cat',
        ]);
        $this->assertTrue($channel->isTagAccepted('cat'), 'channel is accepting only "poney" and "cat", so "cat" should be accepted.');
        $this->assertFalse($channel->isTagAccepted('window'), 'channel is accepting only "poney" and "cat", so "window" should be rejected.');
    }

    public function testingAreTagsAccepted()
    {
        /** channel with no filter accept everything */
        $channel = factory(Channel::class)->create();
        $this->assertTrue($channel->areTagsAccepted(['window', 'house']), 'channel is filtering nothing, tag should be accepted.');

        /** channel with some only tags to accept */
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'poney, cat, dog, chicken',
        ]);
        $this->assertTrue($channel->areTagsAccepted(['cat', 'mouse']), 'cat is one of the tags that channel is accepting so it should be accepted.');
        $this->assertFalse($channel->areTagsAccepted(['window', 'house']), 'neither window nor house is in the list of allowed tags so it should be rejected.');

        // filtering by date should change nothing
        $channel = factory(Channel::class)->create([
            'accept_video_by_tag' => 'poney, cat, dog, chicken',
            'reject_video_too_old' => Carbon::parse('10 years ago')
        ]);
        $this->assertTrue($channel->areTagsAccepted(['cat', 'mouse']), 'cat is one of the tags that channel is accepting so it should be accepted.');
        $this->assertFalse($channel->areTagsAccepted(['window', 'house']), 'neither window nor house is in the list of allowed tags so it should be rejected.');
    }

    public function testIsDateAccepted()
    {
        /** 
         * channel does not care about video too old 
         * all should be accepted
         */
        $channel = factory(Channel::class)->create();
        $this->assertTrue($channel->isDateAccepted(Carbon::parse("first day of 2009")));
        $this->assertTrue($channel->isDateAccepted(Carbon::now()));

        /**
         * channel is caring about old videos 
         * only those published after december 2019 should be accepted
         */
        $channel = factory(Channel::class)->create([
            'reject_video_too_old' => Carbon::parse("first day of 2020")
        ]);
        $this->assertTrue($channel->isDateAccepted(Carbon::now()));
        $this->assertFalse($channel->isDateAccepted(Carbon::parse("first day of 2019")));
    }
}
