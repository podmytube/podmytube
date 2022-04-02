<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class IndexAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_everyone_is_allowed(): void
    {
        $this->get(route('www.index'))
            ->assertSuccessful()
        ;
    }
}
