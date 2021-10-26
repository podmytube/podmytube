<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CockpitAccessTest extends TestCase
{
    public function test_everyone_is_allowed(): void
    {
        $this->get(route('cockpit.index'))
            ->assertSuccessful()
        ;
    }
}
