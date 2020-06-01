<?php

namespace App\Console\Commands;

use App\Channel;
use App\Mail\ChannelHasReachedItsLimits;
use App\Mail\ChannelIsRegistered;
use App\Mail\WelcomeToPodmytube;
use App\Media;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {emailAddress=frederick@podmytube.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is allowing me to send test email to myself (by default) and check if everything is fine.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->availableEmails = [
            1 => ['label' => 'A new user has successfully registered.'],
            2 => ['label' => 'A new channel has been registered.'],
            3 => ['label' => 'Channel has reached its limits.'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (
            filter_var(
                $email = $this->argument('emailAddress'),
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            throw new \InvalidArgumentException(
                "Email address {$email} is not valid !"
            );
        }

        $this->comment('Here are the mails you can send :');
        foreach ($this->availableEmails as $id => $availableEmail) {
            $this->info($id . ' - ' . $availableEmail['label']);
        }

        $emailIdToSend = $this->ask('Which one do you want to send ?');

        if (
            !in_array(
                $emailIdToSend,
                $allowedIds = array_keys($this->availableEmails)
            )
        ) {
            $this->error(
                'Only number (' . implode(', ', $allowedIds) . ') are accepted.'
            );
            exit(1);
        }

        switch ($emailIdToSend) {
            case 1:
                Mail::to($email)->send(new WelcomeToPodmytube(User::first()));
                break;
            case 2:
                Mail::to($email)->send(
                    new ChannelIsRegistered(Channel::first())
                );
                break;
            case 3:
                Mail::to($email)->send(
                    new ChannelHasReachedItsLimits(Media::first())
                );
                break;
        }

        #$class = ChannelIsRegistered::class;
        #Mail::to($email)->send(new $class($channel->user, $channel));

        $this->comment(
            'Email {' .
                $this->availableEmails[$emailIdToSend]['label'] .
                "} has been sent to {$email}."
        );
    }
}
