<?php

namespace Tests\Unit;

use App\StripePlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PlansTableSeeder;
use StripePlansTableSeeder;
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
        /** creating one yearly */
        $yearlyStripePlan = factory(StripePlan::class)->create(['is_yearly' => true]);

        /** and 2 monthly */
        $expectedMonthlyPlans = 2;
        $monthlyStripePlans = factory(StripePlan::class, $expectedMonthlyPlans)->create(['is_yearly' => false]);

        /** getting yearly */
        $result = StripePlan::yearly();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($yearlyStripePlan->id, $result->first()->id);

        /** getting monthly */
        $results = StripePlan::monthly();
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount($expectedMonthlyPlans, $results);

        /** checking I really have the monthly plans */
        $results->map(function ($stripePlan) use ($monthlyStripePlans) {
            $this->assertTrue($monthlyStripePlans->pluck('id')->contains($stripePlan->id));
        });
    }

    /** @test */
    public function yearly_with_slug()
    {
        Artisan::call('db:seed', ['--class' => PlansTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => StripePlansTableSeeder::class]);
        $results = StripePlan::isYearly()->with(['plan' => function ($query) {$query->whereIn('slug', ['starter', 'business']);}])->get();
        dd($results);
    }
}
