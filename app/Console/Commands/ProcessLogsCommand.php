<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Analytics\LogProcessor;
use App\Console\Commands\Traits\BaseCommand;
use App\Exceptions\ProcessLogsCommandHasFailedException;
use App\Factories\DockerLogsFactory;
use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Ssh\Ssh;
use Throwable;

class ProcessLogsCommand extends Command
{
    use BaseCommand;

    /** @var string The name and signature of the console command. */
    protected $signature = 'process:logs {--check-ssh-only}';

    /** @var string The console command description. */
    protected $description = 'This command is getting logs from a container over ssh';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->warn('This server is not a worker.');

            return 0;
        }

        $this->prologue();

        try {
            $sshProcess = Ssh::create(config('app.podhost_ssh_user'), config('app.podhost_ssh_host'))
                ->usePrivateKey(config('app.sftp_key_path'))
                ->disableStrictHostKeyChecking()
                ->execute(DockerLogsFactory::withParams(
                    containerName: config('app.audio_container_name'),
                    since: now()->subHour()
                )->command())
            ;

            if (!$sshProcess->isSuccessful()) {
                $message = 'docker logs command over ssh has failed with error ' . $sshProcess->getErrorOutput();
                Log::error($message);

                throw new ProcessLogsCommandHasFailedException($message);
            }

            if (!$this->option('check-ssh-only')) {
                LogProcessor::with($sshProcess->getOutput())->process();
            }
        } catch (Throwable $thrown) {
            Log::error($thrown->getMessage());
        }
        $this->epilogue();

        return 0;
    }
}
