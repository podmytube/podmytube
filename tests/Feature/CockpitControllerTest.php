<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Factories\VolumeOnDiskFactory;
use App\Models\Channel;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Downloads;

/**
 * @internal
 *
 * @coversNothing
 */
class CockpitControllerTest extends TestCase
{
    use Downloads;
    use RefreshDatabase;

    protected Plan $starterPlan;

    public function setUp(): void
    {
        parent::setUp();
        $this->starterPlan = Plan::factory()->name('starter')->create();
    }

    public function test_everyone_is_allowed(): void
    {
        $this->get(route('cockpit.index'))
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function should_succeed_with_channel_having_no_plan(): void
    {
        $expectedActiveFeeds = 3;
        $expectingPayingChannels = $expectedActiveFeeds;
        $channels = Channel::factory($expectedActiveFeeds)
            ->active()
            ->createdAt(now()->subDay())
            ->create()
        ;

        $nbMedias = $channels->reduce(function ($carry, Channel $channel): int {
            $channel->subscribeToPlan($this->starterPlan);
            $nbMedias = fake()->numberBetween(1, 3);
            $this->addGrabbedMediasToChannel($channel, $nbMedias);

            return $carry + $nbMedias;
        });

        $downloads = $this->addDownloadsForChannelsMediasDuringPeriod($channels, now()->startOfMonth(), now());

        // adding channels with no plan (Should not occured) !
        $channel = Channel::factory()
            ->active()
            ->createdAt(now())
            ->create()
        ;
        $expectedActiveFeeds++;

        $this->followingRedirects()
            ->get(route('cockpit.index'))
            ->assertSuccessful()
            ->assertSeeTextInOrder([
                'last channel',
                $channel->title(),
                'feeds',
                $expectedActiveFeeds,
                'medias',
                $nbMedias,
                'revenues',
                $expectingPayingChannels * $this->starterPlan->price,
                'volume',
                VolumeOnDiskFactory::init()->formatted(),
                'downloads',
                $downloads,
            ])
        ;
    }
}
