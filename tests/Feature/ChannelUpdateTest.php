<?php

namespace Tests\Feature;

use App\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Channel $channel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = factory(Channel::class)->create(['lang' => 'FR']);
    }

    public function testChangingOnlyLanguageShouldWork()
    {
        $languageExpected = 'PT';
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            //->from(route('channel.edit', $this->channel))
            ->patch(route('channel.update', $this->channel), [
                'lang' => $languageExpected,
            ])
            ->assertSuccessful();
        $this->channel->refresh();
        $this->assertEquals($languageExpected, $this->channel->lang);
    }

    public function testUpdateShouldRun()
    {
        $languageExpected = 'EN';
        $this->followingRedirects()
            ->actingAs($this->channel->user)
            //->from(route('channel.edit', $this->channel))
            ->patch(route('channel.update', $this->channel), [
                'podcast_title' => 'Another title',
                'authors' => 'John Doe',
                'email' => 'john.doe@gmail.com',
                'lang' => $languageExpected,
            ])
            ->assertSuccessful();
        $this->channel->refresh();
        $this->assertEquals($languageExpected, $this->channel->lang);
    }
}
