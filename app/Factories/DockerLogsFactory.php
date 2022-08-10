<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\DockerLogsCommandHasFailedException;
use Illuminate\Support\Facades\Log;
use Spatie\Ssh\Ssh;

class DockerLogsFactory
{
    protected string $containerName;
    protected string $sshUser;
    protected string $sshHost;
    protected string $sshPrivateKeyPath;
    protected ?string $since = null;
    protected ?int $tail = null;

    private function __construct(string $containerName, string $sshUser, string $sshHost, string $sshPrivateKeyPath)
    {
        $this->containerName = $containerName;
        $this->sshUser = $sshUser;
        $this->sshHost = $sshHost;
        $this->sshPrivateKeyPath = $sshPrivateKeyPath;
    }

    public static function forContainer(...$params)
    {
        return new static(...$params);
    }

    public function defineSince(string $since): self
    {
        $this->since = $since;

        return $this;
    }

    /**
     * set the number of line from the end of log we need.
     */
    public function defineTail(int $tail): self
    {
        $this->tail = $tail;

        return $this;
    }

    public function tail(): ?int
    {
        return $this->tail;
    }

    public function since(): ?string
    {
        return $this->since;
    }

    public function containerName(): string
    {
        return $this->containerName;
    }

    public function sshUser(): string
    {
        return $this->sshUser;
    }

    public function sshHost(): string
    {
        return $this->sshHost;
    }

    public function sshPrivateKeyPath(): string
    {
        return $this->sshPrivateKeyPath;
    }

    public function commandToRun()
    {
        $commandLine = 'docker logs';
        if ($this->since() !== null) {
            $commandLine .= ' --since=' . $this->since();
        } elseif ($this->tail() !== null) {
            $commandLine .= ' --tail=' . $this->tail();
        }

        $commandLine .= " {$this->containerName()}";

        return $commandLine; //'docker logs --since=' . $this->since() . ' ' . $this->containerName();
    }

    public function run(): self
    {
        $sshProcess = Ssh::create($this->sshUser(), $this->sshHost())
            ->usePrivateKey($this->sshPrivateKeyPath())
            ->disableStrictHostKeyChecking()
            ->execute($this->commandToRun())
        ;

        if (!$sshProcess->isSuccessful()) {
            $message = 'docker logs command over ssh has failed with error ' . $sshProcess->getErrorOutput();
            Log::error($message);

            throw new DockerLogsCommandHasFailedException($message);
        }

        $this->output = $sshProcess->getOutput();

        return $this;
    }

    public function output(): string
    {
        return $this->output;
    }
}
