<?php

namespace Tests\Unit;

use App\StripePlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripePlanModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function by_period_is_ok()
    {
        $yearlyStripePlan = factory(StripePlan::class)->create(['is_yearly' => true]);
        $expectedMonthlyPlans = 2;
        $monthlyStripePlans = factory(StripePlan::class, $expectedMonthlyPlans)->create(['is_yearly' => false]);

        $result = StripePlan::yearly();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($yearlyStripePlan->id, $result->first()->id);

        $results = StripePlan::monthly();
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount($expectedMonthlyPlans, $results);

        $results->map(function ($stripePlan) use ($monthlyStripePlans) {
            $this->assertTrue($monthlyStripePlans->pluck('id')->contains($stripePlan->id));
        });
        $this->assertEquals($yearlyStripePlan->id, $result->first()->id);
    }
}
