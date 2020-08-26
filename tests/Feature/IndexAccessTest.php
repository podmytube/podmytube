<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexAccessTest extends TestCase
{
    use RefreshDatabase;

    public function testEveryoneIsAllowed()
    {
        $this->get('https://' . env('WWW_DOMAIN') . '/')
            ->assertSuccessful()
            ->assertSeeInOrder([
                'Home',
                'Features',
                'Plans',
                'About',
                'Login',
                'Register',
                'Youtube business',
                'Get started free',
                'Pricing',
                'About Podmytube',
            ]);
    }
}
