<?php

namespace Tests\Unit;

use \App\Subscription;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupscriptionsMigrationTest extends TestCase
{
    
    public function setUp()
    {
        var_dump(Subscription::all()->count());
        if(Subscription::all()->count()<=0){
            die("There is no subscription to check => exit !");
        }
    }

    /**
     * @test
     */
    public function checkThatFreeChannelHasNoSubscription()
    {
        $this->assertEqual(0, 0);
    }
}
