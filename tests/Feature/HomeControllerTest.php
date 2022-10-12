<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->verifiedAt(now())->create();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function guest_should_be_rejected(): void
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    /** @test */
    public function verified_user_should_access_home(): void
    {
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSeeText('You have no podcast at this time.')
            ->assertSeeText("It's time to transform your channel into a podcast.", escape: false)
        ;
    }

    /** @test */
    public function non_verified_user_should_see_verification_message(): void
    {
        /** @var Authenticatable $nonVerifiedUser */
        $nonVerifiedUser = User::factory()->create();
        $this->followingRedirects()
            ->actingAs($nonVerifiedUser)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertSeeText(' Your podcast will only be generated once your email address is validated')
            ->assertSee('<form action="' . route('verification.send') . '" method="POST">', escape: false)
        ;
    }

    /** @test */
    public function non_verified_user_with_verification_sent_should_not_see_verification_message(): void
    {
        /** @var Authenticatable $nonVerifiedUser */
        $nonVerifiedUser = User::factory()->create();

        $this->followingRedirects()
            ->actingAs($nonVerifiedUser)
            ->withSession(['verification_sent' => true])
            ->get(route('home'))
            ->assertSuccessful()
            ->assertDontSee(' Your podcast will only be generated once your email address is validated')
            ->assertDontSee('<form action="' . route('verification.send') . '" method="POST">', escape: false)
        ;
    }

    /** @test */
    public function verified_user_should_see_his_channels(): void
    {
        // associating channel to user
        $this->channel->update(['user_id' => $this->user->id()]);

        /** adding another channel */
        $anotherChannel = Channel::factory()->create(['user_id' => $this->user->id()]);
        $anotherChannel->subscribeToPlan(Plan::factory()->create());

        $this->user->refresh();
        $this->followingRedirects()
            ->actingAs($this->user)
            ->get(route('home'))
            ->assertSuccessful()
            ->assertViewIs('home')
            ->assertSee(
                [
                    $this->channel->title(),
                    $anotherChannel->title(),
                ]
            )
        ;
    }
}
