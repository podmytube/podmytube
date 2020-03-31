<?php

namespace Tests\Feature;

use App\Channel;
use Tests\TestCase;
use App\Jobs\MailChannelIsRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MailChannelIsRegisteredTest extends TestCase
{
    use RefreshDatabase;

    protected static $db_inited = false;
    
    protected static $channel;

    protected static function initChannel()
    {
        self::$channel = factory(Channel::class)->create(['email' => 'ftyteca@gmail.com']);
    }

    public function setUp(): void
    {
        parent::setUp();

        if (!self::$db_inited) {
            static::$db_inited = true;
            static::initChannel();
        }
    }

    public function testSendingMailForNewlyRegisteredChannelIsWorking ()
    {
        $mailObj = new MailChannelIsRegistered(self::$channel);
        $mailObj->handle();
        $this->assertGreaterThan(0, Channel::all()->count());
    }
}
