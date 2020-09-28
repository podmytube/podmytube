<?php

namespace Tests\Unit;

use App\Channel;
use App\Modules\LastMediaChecker;
use App\Plan;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LastMediaCheckerTest extends TestCase
{
    use RefreshDatabase;

    public const DELAY_IN_HOURS = 6;
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    public function testMediaShouldBeGrabbed()
    {
        $channel = factory(Channel::class)->create(['channel_id' => self::PERSONAL_CHANNEL_ID,]);
        factory(Subscription::class)->create(
            [
                'channel_id' => $channel->channel_id,
                'plan_id' => Plan::bySlug('weekly_youtuber'),
            ]
        );
        LastMediaChecker::for($channel)->shouldItBeGrabbed();
    }

    public function testMediaShouldHaveBeenGrabbed()
    {
        $this->markTestIncomplete("to be done");
    }
}
