<?php

namespace App\Console\Commands;

use App\Mail\Newsletter;
use App\Mail\WelcomeToPodmytube;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewsletter extends Command
{
    /** @var string $signature The name and signature of the console command. */
    protected $signature = 'email:newsletter';

    /** @var string $description The console command description. */
    protected $description = 'This command is sending one newsletter.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$this->askUserWhichOneToSend();

        /**
         * getting users list
         */
        $users = User::take(1)->get();

        $users->map(function ($user) {
            $mailable = new Newsletter($user);
            /**
             * dispatch
             */
            Mail::to($user)->queue($mailable);
        });

        $this->comment(
            "{$users->count()} newsletters were successfully queued.",
            'v'
        );
    }

    protected function askUserWhichOneToSend()
    {
        dd(base_path('resources/views/emails/newsletters/'));
    }
}
