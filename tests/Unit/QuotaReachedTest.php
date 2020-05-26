<?php

namespace Tests\Unit;

use App\Channel;
use App\Events\MediaRegistered;
use App\Media;
use App\Plan;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuotaReachedTest extends TestCase
{
    use RefreshDatabase;

    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';

    protected $channel;
    protected $subscription;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
        $this->channel = factory(Channel::class)->create([
            'channel_id' => self::PERSONAL_CHANNEL_ID,
        ]);
    }

    public function createSubscription(Channel $channel, Plan $plan)
    {
        $this->subscription = factory(Subscription::class)->create([
            'channel_id' => $channel->channel_id,
            'plan_id' => $plan->id,
        ]);
    }

    public function testChannelHasNotReachedItsQuotaNoMailIsSent()
    {
        Mail::fake();
        $this->createSubscription(
            $this->channel,
            Plan::find(Plan::FREE_PLAN_ID)
        );
        factory(Media::class, 1)->create([
            'channel_id' => $this->channel->channel_id,
        ]);
        $this->assertFalse($this->subscription->channel->hasReachedItslimit());
        /** there is only one channel in test db */
        $this->expectsEvents(MediaRegistered::class);
        Artisan::call('channel:update');
        Mail::assertNothingSent();
    }

    public function testChannelHasReachedItsQuotaOneMailIsSent()
    {
        Mail::fake();
        $this->createSubscription(
            $this->channel,
            Plan::find(Plan::FREE_PLAN_ID)
        );
        factory(Media::class, 2)->create([
            'channel_id' => $this->channel->channel_id,
        ]);
        $this->assertTrue($this->subscription->channel->hasReachedItslimit());
        /** there is only one channel in test db */
        $this->expectsEvents(MediaRegistered::class);
        Artisan::call('channel:update');
        
    }
}
