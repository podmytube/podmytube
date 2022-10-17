<?php

declare(strict_types=1);

namespace Tests\Unit\Mailable;

use App\Mail\MonthlyReportMail;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MonthlyReportMailTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;
    protected Plan $freePlan;
    protected Plan $earlyPlan;
    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();

        $this->freePlan = Plan::factory()->isFree()->create();
        $this->earlyPlan = Plan::factory()->name('early')->create(['price' => 0]);
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    /** @test */
    public function default_period_subject_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $expectedSubject = 'Here is your ' . now()->englishMonth . ' ' . now()->year . " report for {$this->channel->channel_name}";
        $mailContent = new MonthlyReportMail($this->channel);
        $this->assertEquals($expectedSubject, $mailContent->getSubject());
    }

    /** @test */
    public function specified_period_subject_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $expectedSubject = 'Here is your ' . now()->subMonths(2)->englishMonth . ' ' . now()->subMonths(2)->year . " report for {$this->channel->channel_name}";
        $mailContent = new MonthlyReportMail($this->channel, now()->subMonths(2));
        $this->assertEquals($expectedSubject, $mailContent->getSubject());
    }

    /** @test */
    public function default_email_for_paying_channel_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);

        $mailContent = new MonthlyReportMail($this->channel, now());

        $mailContent
            ->assertSeeInHtml("Hello {$this->channel->user->firstname}")
            ->assertSeeInHtml("To know what's coming next on Podmytube, you should take a look (and vote) on")
            ->assertSeeInHtml('<a href="https://twitter.com/podmytube" class="text-gray-900 underline" target="_blank">@podmytube</a>')
            ->assertDontSeeInHtml('<a href="' . route('plans.index', $this->channel) . '" class="button">Upgrade</a>')
            ->assertSeeInHtml('No media published this month')
        ;
    }

    /** @test */
    public function default_email_for_non_paying_channel_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan($this->freePlan);
        $mailContent = new MonthlyReportMail($this->channel, now());

        $mailContent
            ->assertSeeInHtml('<a href="' . route('plans.index', $this->channel) . '" class="button">Upgrade</a>')
            ->assertSeeInHtml('No media published this month')
        ;
    }

    /** @test */
    public function default_email_for_early_bird_channel_is_fine(): void
    {
        $this->channel = $this->createChannelWithPlan($this->earlyPlan);
        $mailContent = new MonthlyReportMail($this->channel, now());

        $mailContent
            ->assertDontSeeInHtml('<a href="' . route('plans.index', $this->channel) . '" class="button">Upgrade</a>')
            ->assertSeeInHtml('No media published this month')
        ;
    }

    /** @test */
    public function default_email_for_channel_with_medias_is_fine_too(): void
    {
        $this->channel = $this->createChannelWithPlan($this->starterPlan);
        $this->addGrabbedMediasToChannel($this->channel, 3);

        $mailContent = new MonthlyReportMail($this->channel, now());

        $mailContent
            ->assertDontSeeInHtml('<a href="' . route('plans.index', $this->channel) . '" class="button">Upgrade</a>')
            ->assertDontSeeInHtml('No media published this month')
        ;

        $this->channel->medias->each(function (Media $media) use ($mailContent): void {
            $mailContent->assertSeeInHtml($media->title)
                ->assertSeeInHtml($media->published_at->toFormattedDateString())
                ->assertSeeInHtml($media->statusEmoji())
            ;
        });
    }
}
