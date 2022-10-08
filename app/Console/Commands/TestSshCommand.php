<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use Illuminate\Console\Command;

class TestSshCommand extends Command
{
    use BaseCommand;

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
        $this->prologue();

        $sshProcess = sshPod()->execute('whoami');

        if ($sshProcess->isSuccessful()) {
            $this->comment("It's a success.");
            $errCode = 0;
        } else {
            $this->error('Too bad, there is something wrong.');
            $errCode = 1;
        }

        $this->epilogue();

        return $errCode;
    }
}
