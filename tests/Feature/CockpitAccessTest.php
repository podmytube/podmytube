<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CockpitAccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seedStripePlans(true);
    }

    public function test_everyone_is_allowed(): void
    {
        $this->get(route('cockpit.index'))
            ->assertSuccessful()
        ;
    }
}
