<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Exceptions\DockerLogsCommandHasFailedException;
use App\Factories\DockerLogsFactory;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DockerLogsFactoryTest extends TestCase
{
    protected string $containerName;
    protected string $sshUser;
    protected string $sshHost;
    protected string $sshPrivateKeyPath;

    public function setUp(): void
    {
        parent::setUp();
        $this->containerName = config('app.audio_container_name');
        $this->sshUser = config('app.sftp_user');
        $this->sshHost = config('app.sftp_host');
        $this->sshPrivateKeyPath = config('app.sftp_key_path');
    }

    /** @test */
    public function everything_should_be_fine(): void
    {
        $expectedNumberOfLogs = 10;
        $dockerLogs = DockerLogsFactory::forContainer($this->containerName, $this->sshUser, $this->sshHost, $this->sshPrivateKeyPath)
            ->defineTail(10)
        ;
        $this->assertNotNull($dockerLogs);
        $this->assertInstanceOf(DockerLogsFactory::class, $dockerLogs);

        array_map(function ($property) use ($dockerLogs): void {
            $this->assertEquals($this->{$property}, $dockerLogs->{$property}());
        }, [
            'containerName',
            'sshUser',
            'sshHost',
            'sshPrivateKeyPath',
        ]);

        $output = $dockerLogs->run()->output();
        $this->assertNotNull($output);
        $this->assertIsString($output);
        $this->assertGreaterThan($expectedNumberOfLogs, $output);
    }

    /** @test */
    public function invalid_ssh_user_should_fail(): void
    {
        $this->markAsRisky('This test will cause you to be jailed by fail2ban');
        $this->expectException(DockerLogsCommandHasFailedException::class);
        DockerLogsFactory::forContainer($this->containerName, 'invalid-user', $this->sshHost, $this->sshPrivateKeyPath)
            ->run()
        ;
    }

    /** @test */
    public function invalid_ssh_host_should_fail(): void
    {
        $this->markAsRisky('This test will cause you to be jailed by fail2ban');
        $this->expectException(DockerLogsCommandHasFailedException::class);
        DockerLogsFactory::forContainer($this->containerName, $this->sshUser, 'invalid-host', $this->sshPrivateKeyPath)
            ->run()
        ;
    }

    /** @test */
    public function invalid_private_key_should_fail(): void
    {
        $this->markAsRisky('This test will cause you to be jailed by fail2ban');
        $this->expectException(DockerLogsCommandHasFailedException::class);
        DockerLogsFactory::forContainer($this->containerName, $this->sshUser, $this->sshHost, '/this/is/not/valid/private/key')
            ->run()
        ;
    }

    /** @test */
    public function invalid_container_name_should_fail(): void
    {
        $this->expectException(DockerLogsCommandHasFailedException::class);
        DockerLogsFactory::forContainer('this-will-never-be-a-container-name', $this->sshUser, $this->sshHost, $this->sshPrivateKeyPath)
            ->run()
        ;
    }
}
