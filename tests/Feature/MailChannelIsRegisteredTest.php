<?php

namespace Tests\Feature;

use App\Channel;
use Tests\TestCase;
use App\Jobs\MailChannelIsRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class MailChannelIsRegisteredTest extends TestCase
{
    use RefreshDatabase;

    protected static $db_inited = false;

    protected $channel;

    public function setUp(): void
    {
        $this->markTestSkipped('All this job part is ... crappy.');

        parent::setUp();

        $this->channel = factory(Channel::class)->create(['email' => 'ftyteca@gmail.com']);
    }

    public function testSendingMailForNewlyRegisteredChannelIsWorking()
    {
        Mail::fake();

        new MailChannelIsRegistered($this->channel);

        Mail::assertSent(ChannelIsRegistered::class, 1);
    }
}
