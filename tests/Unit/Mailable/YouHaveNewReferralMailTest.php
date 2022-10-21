<?php

declare(strict_types=1);

namespace Tests\Unit\Mailable;

use App\Mail\YouHaveNewReferralMail;
use App\Models\Channel;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class YouHaveNewReferralMailTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $starterPlan;
    protected User $referrer;
    protected User $referral;

    public function setUp(): void
    {
        parent::setUp();

        $this->starterPlan = Plan::factory()->name('starter')->create();
        $this->referrer = User::factory()->create();
        $this->referral = User::factory()->withReferrer($this->referrer)->create();
        $this->channel = $this->createChannel($this->referral, $this->starterPlan);
    }

    /** @test */
    public function new_referral_email_is_fine(): void
    {
        $mailContent = new YouHaveNewReferralMail($this->channel);

        $mailContent->assertSeeInOrderInHtml([
            "Congratulations {$this->referrer->firstname}",
            "Channel <b><a href=\"{$this->channel->youtubeUrl()}\">{$this->channel->title()}</a></b> has been registered",
            "by one of your referral ({$this->referral->firstname}).",
            "The chosen plan was {$this->starterPlan->name} at {$this->starterPlan->price}",
            'If you have any question, feel free to answer this email.',
            'Cheers.',
            'Fred',
        ]);
    }
}
