<?php

namespace Tests\Feature;

use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        $this->markTestSkipped('This test is failing because of strange relationship handling with sqlite');
        parent::setUp();
        $subscription = factory(Subscription::class)->create();
        $this->channel = $subscription->channel;
    }

    /**
     * @dataProvider provideValidData
     */
    public function testValidData(string $message, array $data)
    {
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            ->patch(route('channel.update', $this->channel), $data)
            ->assertSuccessful();
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testInvalidData(string $message, array $data, $error)
    {
        $this->actingAs($this->channel->user)
            ->from(route('channel.edit', $this->channel))
            ->patch(route('channel.update', $this->channel), $data)
            ->assertSessionHasErrors($error)
            ->assertRedirect(route('channel.edit', $this->channel));
    }

    public function provideValidData()
    {
        return [
            ['title only should be valid', ['podcast_title' => 'Great podcast means great responsibilities']],
            ['only explicit', ['explicit' => 0]],
            ['only description', ['description' => 'Lorem ipsum dolore sit amet.']],
            ['only lang', ['lang' => 'FR']],
            ['title and explicit', ['podcast_title' => 'Great podcast means great responsibilities', 'explicit' => 1]],
            ['Category should be valid', ['category_id' => 1]],
        ];
    }

    public function provideInvalidData()
    {
        /**
         * format is message, data to PATCH, field in error
         */
        return [
            ['invalid link', ['link' => 'invalid url'], 'link'],
            ['link without http', ['link' => 'google.com'], 'link'],
            ['link without domain', ['link' => 'https://'], 'link'],
            ['invalid email', ['email' => 'invalid email'], 'email'],
            ['invalid lang', ['lang' => 'JP'], 'lang'],
            ['invalid category', ['category_id' => 'not a category'], 'category_id'],
            ['invalid explicit', ['explicit' => 'not a boolean'], 'explicit'],
        ];
    }
}
