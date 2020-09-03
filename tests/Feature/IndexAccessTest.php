<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexAccessTest extends TestCase
{
    public function testEveryoneIsAllowed()
    {
        $this->get(route('www.index'))
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
