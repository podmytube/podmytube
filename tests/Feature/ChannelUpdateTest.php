<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\ChannelUpdatedEvent;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        Event::fake();
    }

    /**
     * @dataProvider provideValidData
     */
    public function test_valid_data(array $data): void
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('channel.update', $this->channel), $data)
            ->assertSuccessful()
        ;

        Event::assertDispatched(ChannelUpdatedEvent::class);
    }

    /**
     * @dataProvider provideInvalidData
     *
     * @param mixed $error
     */
    public function test_invalid_data(array $data, $error): void
    {
        $this->actingAs($this->channel->user)
            ->from(route('channel.edit', $this->channel))
            ->patch(route('channel.update', $this->channel), $data)
            ->assertSessionHasErrors($error)
            ->assertRedirect(route('channel.edit', $this->channel))
        ;

        Event::assertNotDispatched(ChannelUpdatedEvent::class);
    }

    public function provideValidData()
    {
        return [
            'podcat title' => [['podcast_title' => 'Great podcast means great responsibilities']],
            'explicit' => [['explicit' => 0]],
            'description' => [['description' => 'Lorem ipsum dolore sit amet.']],
            'lang' => [['lang' => 'FR']],
            'podcast_title' => [['podcast_title' => 'Great podcast means great responsibilities', 'explicit' => 1]],
            'category_id' => [['category_id' => 1]],
        ];
    }

    public function provideInvalidData()
    {
        return [
            'not an url should fail' => [['link' => 'invalid url'], 'link'],
            'not a complete url' => [['link' => 'google.com'], 'link'],
            'not a complete url' => [['link' => 'https://'], 'link'],
            'invalid email' => [['email' => 'invalid email'], 'email'],
            'unknown language id' => [['language_id' => 9999], 'language_id'],
            'unknown category id' => [['category_id' => 'not a category'], 'category_id'],
            'explicit not a boolean' => [['explicit' => 'not a boolean'], 'explicit'],
        ];
    }
}
