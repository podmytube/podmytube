<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Mail\Newsletter;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendNewsletter extends Command
{
    use BaseCommand;

    /** @var string The name and signature of the console command. */
    protected $signature = 'email:newsletter';

    /** @var string The console command description. */
    protected $description = 'This command is sending one newsletter.';

    /**
     * @var array
     *            list of newsletter files that that are in
     *            resources/views/emails/newsletters/2020-06-free-plan-update.blade.php
     */
    protected $availableNewsletters = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->prologue();
        $this->availableNewsletters();

        $newsletterToSend = $this->askUserWhichOneToSend();
        if ($newsletterToSend === false) {
            $this->error('This newsletter does not exist.');

            return 1;
        }

        /**
         * getting users list.
         */
        $users = User::where('newsletter', '=', true)->get();

        // sending newsletter to every user
        $users->map(function ($user) use ($newsletterToSend): void {
            $mailable = new Newsletter($user, $newsletterToSend);
            // dispatch
            Mail::to($user)->queue($mailable);
        });

        $this->comment(
            "{$users->count()} newsletters were successfully queued.",
            'v'
        );

        $this->epilogue();

        return 0;
    }

    protected function askUserWhichOneToSend()
    {
        if (count($this->availableNewsletters) === 1) {
            return $this->availableNewsletters[0];
        }

        $this->comment('Here are the mails you can send :');
        $i = 0;
        foreach ($this->availableNewsletters as $newsletter) {
            $this->info($i . ' - ' . $newsletter);
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

    protected function availableNewsletters(): void
    {
        foreach (Storage::disk('newsletter')->files() as $newsletter) {
            $this->availableNewsletters[] = explode('.', $newsletter)[0];
        }
    }
}
