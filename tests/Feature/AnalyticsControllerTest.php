<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\AnalyticsController;
use App\Models\Channel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AnalyticsControllerTests extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function user_should_see_his_channel_analytics(): void
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->get(route('analytics', $this->channel))
            ->assertSuccessful()
            ->assertViewIs('analytics.show')
            ->assertSeeText('Analytics')
        ;
    }

    /** @test */
    public function only_the_owner_should_see_his_channel_analytics(): void
    {
        /** @var Authenticatable $notTheOwner */
        $notTheOwner = User::factory()->create();
        $this->actingAs($notTheOwner)
            ->get(route('analytics', $this->channel))
            ->assertForbidden()
        ;
    }

    /** @test */
    public function guest_should_be_redirected(): void
    {
        $this->get(route('analytics', $this->channel))->assertRedirect('/login');
    }

    /** @test */
    public function null_period_should_return_cur_month_dates(): void
    {
        $controller = new AnalyticsController();
        $this->assertNotNull($controller);
        $this->assertInstanceOf(AnalyticsController::class, $controller);

        $result = $controller->fromPeriodToDates();
        $this->basicCheckingOfFromPeriodToDates($result);

        $this->assertEquals(now()->startOfMonth(), $result[0]);
        $this->assertEquals(now()->endOfMonth(), $result[1]);
    }

    /** @test */
    public function default_period_should_return_cur_month_dates(): void
    {
        $result = (new AnalyticsController())->fromPeriodToDates(AnalyticsController::DEFAULT_PERIOD);
        $this->basicCheckingOfFromPeriodToDates($result);

        $this->assertEquals(now()->startOfMonth(), $result[0]);
        $this->assertEquals(now()->endOfMonth(), $result[1]);
    }

    /** @test */
    public function this_month_period_should_return_cur_month_dates(): void
    {
        $result = (new AnalyticsController())->fromPeriodToDates(AnalyticsController::PERIOD_THIS_MONTH);
        $this->basicCheckingOfFromPeriodToDates($result);

        $this->assertEquals(now()->startOfMonth(), $result[0]);
        $this->assertEquals(now()->endOfMonth(), $result[1]);
    }

    /** @test */
    public function this_week_period_should_return_cur_week_dates(): void
    {
        $result = (new AnalyticsController())->fromPeriodToDates(AnalyticsController::PERIOD_THIS_WEEK);
        $this->basicCheckingOfFromPeriodToDates($result);

        $this->assertEquals(now()->startOfWeek(weekStartsAt: Carbon::MONDAY), $result[0]);
        $this->assertEquals(now()->endOfWeek(weekEndsAt: Carbon::SUNDAY), $result[1]);
    }

    /** @test */
    public function last_month_period_should_return_last_month_dates(): void
    {
        $result = (new AnalyticsController())->fromPeriodToDates(AnalyticsController::PERIOD_LAST_MONTH);
        $this->basicCheckingOfFromPeriodToDates($result);

        $this->assertEquals(now()->subMonth()->startOfMonth(), $result[0]);
        $this->assertEquals(now()->subMonth()->endOfMonth(), $result[1]);
    }

    /** @test */
    public function last_week_period_should_return_cur_week_dates(): void
    {
        $result = (new AnalyticsController())->fromPeriodToDates(AnalyticsController::PERIOD_LAST_WEEK);
        $this->basicCheckingOfFromPeriodToDates($result);

        $this->assertEquals(now()->subWeek()->startOfWeek(weekStartsAt: Carbon::MONDAY), $result[0]);
        $this->assertEquals(now()->subWeek()->endOfWeek(weekEndsAt: Carbon::SUNDAY), $result[1]);
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    protected function basicCheckingOfFromPeriodToDates($result): void
    {
        $this->assertNotNull($result);
        $this->assertIsArray($result);
        // should contain start and end dates (Carbon::class) both
        $this->assertCount(2, $result);

        array_map(function ($item): void {
            $this->assertNotNull($item);
            $this->assertInstanceOf(Carbon::class, $item);
        }, $result);
    }
}
