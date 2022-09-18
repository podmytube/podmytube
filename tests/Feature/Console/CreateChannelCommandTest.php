<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

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
class CreateChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    protected const DEFAULT_USER_ID = 1;
    protected const DEFAULT_PLAN_ID = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedApiKeys();
        $this->seedCategories();

        // because we need one (the 1 at least)
        Plan::factory()->create();
        User::factory()->create();
    }

    /** @test */
    public function unknown_channel_id_should_fail(): void
    {
        $this->artisan('create:channel', ['channel_id' => 'unknown_channel_id'])
            ->assertExitCode(1)
        ;
    }

    /** @test */
    public function real_channel_id_should_succeed(): void
    {
        // command should run properly
        $this->artisan('create:channel', ['channel_id' => self::PERSONAL_CHANNEL_ID])
            ->assertExitCode(0)
        ;
        $createdChannel = Channel::byChannelId(self::PERSONAL_CHANNEL_ID);
        $this->assertNotNull($createdChannel);
        $this->assertEquals(self::PERSONAL_CHANNEL_ID, $createdChannel->channel_id);
        $this->assertEquals(self::DEFAULT_USER_ID, $createdChannel->userId());
        $this->assertEquals(self::DEFAULT_PLAN_ID, $createdChannel?->subscription?->plan?->id);
    }

    /** @test */
    public function real_channel_id_with_user_should_succeed(): void
    {
        $anotherUser = User::factory()->create();
        // command should run properly
        $this->artisan('create:channel', [
            'channel_id' => self::PERSONAL_CHANNEL_ID,
            '--userId' => $anotherUser->id(),
        ])
            ->assertExitCode(0)
        ;

        $createdChannel = Channel::byChannelId(self::PERSONAL_CHANNEL_ID);
        $this->assertNotNull($createdChannel);
        $this->assertEquals(self::PERSONAL_CHANNEL_ID, $createdChannel->channel_id);
        $this->assertEquals($anotherUser->id(), $createdChannel->userId());
        $this->assertEquals(self::DEFAULT_PLAN_ID, $createdChannel?->subscription?->plan?->id);
    }

    /** @test */
    public function real_channel_id_with_user_and_plan_should_succeed(): void
    {
        $anotherUser = User::factory()->create();
        $anotherPlan = Plan::factory()->create();

        // command should run properly
        $this->artisan('create:channel', [
            'channel_id' => self::PERSONAL_CHANNEL_ID,
            '--userId' => $anotherUser->id(),
            '--planId' => $anotherPlan->id,
        ])
            ->assertExitCode(0)
        ;

        $createdChannel = Channel::byChannelId(self::PERSONAL_CHANNEL_ID);
        $this->assertNotNull($createdChannel);
        $this->assertEquals(self::PERSONAL_CHANNEL_ID, $createdChannel->channel_id);
        $this->assertEquals($anotherUser->id(), $createdChannel->userId());
        $this->assertEquals($anotherPlan->id, $createdChannel?->subscription?->plan?->id);
    }
}
