<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\StripePlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
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
        Artisan::call('db:seed', ['--class' => 'PlansTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'StripePlansTableSeeder']);
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
}
