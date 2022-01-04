<?php

namespace Tests\Feature;

use App\Events\ChannelUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChannelUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
        Event::fake();
    }

    /**
     * @dataProvider provideValidData
     */
    public function testValidData(array $data)
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('channel.update', $this->channel), $data)
            ->assertSuccessful();

        Event::assertDispatched(ChannelUpdated::class);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testInvalidData(array $data, $error)
    {
        $this->actingAs($this->channel->user)
            ->from(route('channel.edit', $this->channel))
            ->patch(route('channel.update', $this->channel), $data)
            ->assertSessionHasErrors($error)
            ->assertRedirect(route('channel.edit', $this->channel));

        Event::assertNotDispatched(ChannelUpdated::class);
    }

    public function provideValidData()
    {
        return [
            [['podcast_title' => 'Great podcast means great responsibilities']],
            [['explicit' => 0]],
            [['description' => 'Lorem ipsum dolore sit amet.']],
            [['lang' => 'FR']],
            [['podcast_title' => 'Great podcast means great responsibilities', 'explicit' => 1]],
            [['category_id' => 1]],
        ];
    }

    public function provideInvalidData()
    {
        /**
         * format is message, data to PATCH, field in error
         */
        return [
            [['link' => 'invalid url'], 'link'],
            [['link' => 'google.com'], 'link'],
            [['link' => 'https://'], 'link'],
            [['email' => 'invalid email'], 'email'],
            [['language_id' => 9999], 'language_id'],
            [['category_id' => 'not a category'], 'category_id'],
            [['explicit' => 'not a boolean'], 'explicit'],
        ];
    }
}
