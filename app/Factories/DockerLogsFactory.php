<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\DockerLogsCommandHasFailedException;
use Carbon\Carbon;

class DockerLogsFactory
{
    private function __construct(
        protected string $containerName,
        protected ?Carbon $since = null,
        protected ?Carbon $until = null,
        protected ?int $tail = null,
    ) {
    }

    public static function withParams(string $containerName, ?Carbon $since = null, ?Carbon $until = null, ?int $tail = null)
    {
        return new static(
            containerName: $containerName,
            tail: $tail,
            since: $since,
            until: $until
        );
    }

    public function tail(): ?int
    {
        return $this->tail;
    }

    public function since(): ?string
    {
        return $this->since->setTimezone('UTC')->toDateTimeLocalString();
    }

    public function until(): ?string
    {
        return $this->until->setTimezone('UTC')->toDateTimeLocalString();
    }

    public function containerName(): string
    {
        return $this->containerName;
    }

    public function command()
    {
        $commandLine = 'docker logs';

        if ($this->since !== null) {
            $commandLine .= ' --since ' . $this->since();
        }

        if ($this->until !== null) {
            $commandLine .= ' --until ' . $this->until();
        }

        if ($this->tail !== null) {
            $commandLine .= ' --tail ' . $this->tail();
        }

        $commandLine .= " {$this->containerName()}";
        ray($commandLine);

        return $commandLine; // 'docker logs --since=' . $this->since() . ' ' . $this->containerName();
    }

    /* public function run(): self
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
    } */
}
