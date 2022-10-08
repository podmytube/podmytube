<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\User;
use Illuminate\Console\Command;
use Throwable;

class UserGenerateReferralCodeCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-referral-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add referral code to users who do not have one.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->prologue();

        try {
            User::query()
                ->whereNull('referral_code')
                ->get()
                ->each(fn (User $user) => $user->update(['referral_code' => User::createReferralCode()]))
            ;
        } catch (Throwable $thrown) {
            $this->error($thrown->getMessage());
        }
        $this->epilogue();

        return Command::SUCCESS;
    }
}
