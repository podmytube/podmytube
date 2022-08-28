<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\ServerRole;
use Illuminate\Console\Command;
use Spatie\Ssh\Ssh;

class TestSshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ssh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        $sshProcess = Ssh::create(config('app.podhost_ssh_user'), config('app.podhost_ssh_host'))
            ->usePrivateKey(config('app.sftp_key_path'))
            ->disableStrictHostKeyChecking()
            ->execute('whoami')
        ;

        if ($sshProcess->isSuccessful()) {
            $this->comment("It's a success.");

            return 0;
        }

        $this->alert('Too bad, there is something wrong.');

        return 1;
    }
}
