<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelCreationIsPossible extends TestCase
{
    use RefreshDatabase;

    protected static $db_inited = false;
    protected static $rightPassword = "'i-love-laravel'";

    protected static $user;

    protected static function initUser()
    {
        self::$user = factory(User::class)->create([
            'password' => bcrypt(self::$rightPassword),
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        if (!self::$db_inited) {
            static::$db_inited = true;
            static::initUser();
        }
    }

    public function testChannelCreationIsFine()
    {
        $response = $this->actingAs(self::$user)->from('/channel/create')->post('/channel', [
            "channel_url" => "http://www.youtube.com/channel/UCw6bU9JT_Lihb2pbtqAUGQw-/",
        ]);
        $response->assertRedirect('/home');
        $response->assertViewIs("home");
        $response->assertSessionHasNoErrors();
        //$response->assertViewHas("Cannot get channel information for this channel");
    }
}
