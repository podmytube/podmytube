<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasDomain;

class IndexAccessTest extends TestCase
{
    use RefreshDatabase, HasDomain;

    public function setUp(): void
    {
        parent::setUp();
        $this->setDomain(env('WWW_DOMAIN'));
    }

    public function testEveryoneIsAllowed()
    {
        $this->get($this->getDomain() . '/')
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
