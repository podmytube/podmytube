<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $sshProcess = sshPod()->execute('whoami');

        if ($sshProcess->isSuccessful()) {
            $this->comment("It's a success.");

            return 0;
        }

        $this->alert('Too bad, there is something wrong.');

        return 1;
    }
}
