<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\StripePlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StripePlanModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedStripePlans(true);
    }

    /** @test */
    public function local_stripe_ids_is_working_fine(): void
    {
        $expectedStripeIds = [
            'plan_EfYDgsuNMdj8Sb',
            'plan_EfYBFztmlQ3u4C',
            'plan_EfudBu6TCXHWEg',
            'plan_EfuceKVUwJTt5O',
            'price_1Ia1NzLrQ8vSqYZETFAVb2Fb',
            'price_1Ia1NzLrQ8vSqYZElJhNIc4V',
            'price_1IctnvLrQ8vSqYZEQ2Khysvu',
            'price_1IctnvLrQ8vSqYZEcx9buUYo',
            'price_1IctxLLrQ8vSqYZEKdKkpHsm',
            'price_1IctxLLrQ8vSqYZEg7qP6959',
        ];
        $result = StripePlan::stripeIdsOnly();
        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEqualsCanonicalizing($expectedStripeIds, $result->toArray());
    }

    /**
     * @test
     * @dataProvider provideExpectedPriceIds
     */
    public function getting_stripe_id_by_plan_and_billing_frequency_is_fine(array $provided): void
    {
        array_map(
            function (string $mode) use ($provided): void {
                $plan = $this->getPlanBySlug($provided['slug']);
                $expectedYearlyPriceId = $provided[$mode]['yearly'];
                $expectedMonthlyPriceId = $provided[$mode]['monthly'];

                $isLive = $mode === 'live' ? true : false;

                // yearly
                $result = StripePlan::priceIdForPlanAndBilling($plan, true, $isLive);
                $this->assertNotNull($result);
                $this->assertEquals($expectedYearlyPriceId, $result);

                // monthly
                $result = StripePlan::priceIdForPlanAndBilling($plan, false, $isLive);
                $this->assertNotNull($result);
                $this->assertEquals($expectedMonthlyPriceId, $result);
            },
            ['test', 'live']
        );
    }

    public function provideExpectedPriceIds()
    {
        return [
            [[
                'slug' => 'starter',
                'test' => [
                    'yearly' => 'price_1Ia1NzLrQ8vSqYZETFAVb2Fb',
                    'monthly' => 'price_1Ia1NzLrQ8vSqYZElJhNIc4V',
                ],
                'live' => [
                    'yearly' => 'price_1HmxVLLrQ8vSqYZEFlv2SUpd',
                    'monthly' => 'price_1HmxVLLrQ8vSqYZEOK2BxHfy',
                ],
            ]],
            [[
                'slug' => 'professional',
                'test' => [
                    'yearly' => 'price_1IctnvLrQ8vSqYZEQ2Khysvu',
                    'monthly' => 'price_1IctnvLrQ8vSqYZEcx9buUYo',
                ],
                'live' => [
                    'yearly' => 'price_1IcttMLrQ8vSqYZERib3oMYG',
                    'monthly' => 'price_1IcttNLrQ8vSqYZE2xOQ6HGe',
                ],
            ]],
            [[
                'slug' => 'business',
                'test' => [
                    'yearly' => 'price_1IctxLLrQ8vSqYZEKdKkpHsm',
                    'monthly' => 'price_1IctxLLrQ8vSqYZEg7qP6959',
                ],
                'live' => [
                    'yearly' => 'price_1HmxbYLrQ8vSqYZEdab8H6WN',
                    'monthly' => 'price_1HmxbYLrQ8vSqYZE1Q3qOMt1',
                ],
            ]],
        ];
    }

    /** @test */
    public function by_stripe_id_should_be_good(): void
    {
        $this->assertNull(StripePlan::byStripeId('invalid-live', true));
        $this->assertNull(StripePlan::byStripeId('invalid-test', false));

        $expectedStripeId = 'price_1HmxVLLrQ8vSqYZEFlv2SUpd';
        $result = StripePlan::byStripeId($expectedStripeId);
        $this->assertNotNull($result);
        $this->assertInstanceOf(StripePlan::class, $result);
        $this->assertEquals($expectedStripeId, $result->stripe_live_id);
    }
}
