<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\DockerLogsFactory;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DockerLogsFactoryTest extends TestCase
{
    protected string $containerName;

    public function setUp(): void
    {
        parent::setUp();
        $this->containerName = 'mp3';
    }

    /** @test */
    public function since_should_be_utc(): void
    {
        $now = now();
        $nowUtc = clone $now;
        $expectedSince = $nowUtc->setTimezone('utc')->toDateTimeLocalString();
        $dockerLogsFactory = DockerLogsFactory::withParams($this->containerName, since: $now);
        $this->assertEquals($expectedSince, $dockerLogsFactory->since());
    }

    /** @test */
    public function simple_command_line_should_be_good(): void
    {
        $expectedCommand = 'docker logs ' . $this->containerName;
        $command = DockerLogsFactory::withParams($this->containerName)->command();
        $this->assertEquals($expectedCommand, $command);
    }

    /** @test */
    public function command_line_with_tail_should_be_good(): void
    {
        $expectedCommand = "docker logs --tail 10 {$this->containerName}";
        $command = DockerLogsFactory::withParams(
            containerName: $this->containerName,
            tail: 10,
        )->command();
        $this->assertEquals($expectedCommand, $command);
    }

    /** @test */
    public function command_line_with_since_should_be_good(): void
    {
        $expectedDate = now();
        $expectedUtc = clone $expectedDate;
        $expectedSinceUtc = $expectedUtc->setTimezone('UTC')->toDateTimeLocalString();
        $expectedCommand = "docker logs --since {$expectedSinceUtc} {$this->containerName}";
        $command = DockerLogsFactory::withParams(
            containerName: $this->containerName,
            since: $expectedDate,
        )->command();
        $this->assertEquals($expectedCommand, $command);
    }

    /** @test */
    public function command_line_with_until_should_be_good(): void
    {
        $expectedDate = now();
        $expectedUtc = clone $expectedDate;
        $expectedUntilUtc = $expectedUtc->setTimezone('UTC')->toDateTimeLocalString();
        $expectedCommand = "docker logs --until {$expectedUntilUtc} {$this->containerName}";
        $command = DockerLogsFactory::withParams(
            containerName: $this->containerName,
            until: $expectedDate,
        )->command();
        $this->assertEquals($expectedCommand, $command);
    }

    /** @test */
    public function command_line_with_all_should_be_good(): void
    {
        $now = now();
        $twoHoursAgo = clone $now;
        $twoHoursAgo->subHours(2);

        $expectedSinceUtc = $this->toExpectedUtc($twoHoursAgo);
        $expectedUntilUtc = $this->toExpectedUtc($now);
        $expectedTail = 10;

        $expectedCommand = "docker logs --since {$expectedSinceUtc} --until {$expectedUntilUtc} --tail {$expectedTail} {$this->containerName}";
        $command = DockerLogsFactory::withParams(
            containerName: $this->containerName,
            since: $twoHoursAgo,
            until: $now,
            tail: $expectedTail,
        )->command();
        $this->assertEquals($expectedCommand, $command);
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    public function toExpectedUtc(Carbon $date): string
    {
        return $date->setTimezone('UTC')->toDateTimeLocalString();
    }
}
