<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CommandTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }
    }
}
