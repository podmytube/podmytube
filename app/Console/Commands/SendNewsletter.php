<?php

namespace App\Console\Commands;

use App\Mail\Newsletter;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendNewsletter extends Command
{
    /** @var string $signature The name and signature of the console command. */
    protected $signature = 'email:newsletter';

    /** @var string $description The console command description. */
    protected $description = 'This command is sending one newsletter.';

    /**
     * @var array $availableNewsletters
     * list of newsletter files that that are in
     * resources/views/emails/newsletters/2020-06-free-plan-update.blade.php
     */
    protected $availableNewsletters = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->availableNewsletters();

        $newsletterToSend = $this->askUserWhichOneToSend();
        if ($newsletterToSend === false) {
            $this->error('This newsletter does not exist.');
            return false;
        }

        /**
         * getting users list
         */
        $users = User::all(); //User::take(1)->get();

        /**
         * sending newsletter to every user
         */
        $users->map(function ($user) use ($newsletterToSend) {
            $mailable = new Newsletter($user, $newsletterToSend);
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
        if (count($this->availableNewsletters) == 1) {
            return $this->availableNewsletters[0];
        }

        $this->comment('Here are the mails you can send :');
        $i = 0;
        foreach ($this->availableNewsletters as $newsletter) {
            $this->info("$i - $newsletter");
            $i++;
        }

        $answer = $this->ask('Which one do you want to send ?');
        if (!$this->isValidAnswer($answer)) {
            return false;
        }
        return $this->availableNewsletters[$answer];
    }

    protected function isValidAnswer($answer)
    {
        if (!is_numeric($answer)) {
            return false;
        }

        if (0 > $answer || $answer > count($this->availableNewsletters)) {
            return false;
        }
        return true;
    }

    protected function availableNewsletters()
    {
        foreach (Storage::disk('newsletter')->files() as $newsletter) {
            $this->availableNewsletters[] = explode('.', $newsletter)[0];
        }
    }
}
