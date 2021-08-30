<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Modules\ServerRole;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ServerRoleTest extends TestCase
{
    /** @test */
    public function invalid_role_will_get_display_by_default(): void
    {
        Config::set('app.server_role', 'this-role-does-not-exists');
        $this->assertEquals(ServerRole::DISPLAY, ServerRole::getRole());
    }

    /** @test */
    public function display_role_will_be_fine(): void
    {
        Config::set('app.server_role', 'display');
        $this->assertEquals(ServerRole::DISPLAY, ServerRole::getRole());
    }

    /** @test */
    public function hosting_role_will_be_fine(): void
    {
        Config::set('app.server_role', 'hosting');
        $this->assertEquals(ServerRole::HOSTING, ServerRole::getRole());
    }

    /** @test */
    public function worker_role_will_be_fine(): void
    {
        Config::set('app.server_role', 'worker');
        $this->assertEquals(ServerRole::WORKER, ServerRole::getRole());
    }

    /** @test */
    public function is_worker_should_be_false(): void
    {
        collect(['display', 'hosting'])->each(function (string $role): void {
            Config::set('app.server_role', $role);

            $this->assertFalse(ServerRole::isWorker());
        });
    }

    /** @test */
    public function is_worker_should_be_true(): void
    {
        collect(['worker', 'local'])->each(function (string $role): void {
            Config::set('app.server_role', $role);

            $this->assertTrue(ServerRole::isWorker());
        });
    }

    /** @test */
    public function is_display_should_be_false(): void
    {
        collect(['worker', 'hosting'])->each(function (string $role): void {
            Config::set('app.server_role', $role);

            $this->assertFalse(ServerRole::isDisplay());
        });
    }

    /** @test */
    public function is_display_should_be_true(): void
    {
        collect(['display', 'local'])->each(function (string $role): void {
            Config::set('app.server_role', $role);

            $this->assertTrue(ServerRole::isDisplay());
        });
    }
}
