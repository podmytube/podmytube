<?php

namespace App\Console\Commands;

use App\User;
use App\Channel;
use App\Jobs\MailChannelIsRegistered;
use App\Mail\ChannelIsRegistered;
use App\Podcast\PodcastBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmailForNewlyRegisteredChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:newly {channelId} {emailAddress=frederick@podmytube.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(Test) This command will send one "newly registered email" to specified email. This command exists mainly for tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (filter_var($email = $this->argument('emailAddress'), FILTER_VALIDATE_EMAIL)===false){
            throw new \InvalidArgumentException("Email address {$email} is not valid !");
        }

        $channel = Channel::findOrFail($this->argument('channelId'));

        Mail::to($email)->send(new ChannelIsRegistered($channel->user, $channel));

        $this->comment("Newly registered channel email has been sent to $email.");
    }
}
