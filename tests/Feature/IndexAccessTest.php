<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexAccessTest extends TestCase
{
    public function testEveryoneIsAllowed()
    {
        $this->get(route('www.index'))
            ->assertSuccessful();
    }
}
