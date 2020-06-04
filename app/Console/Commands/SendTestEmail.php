<?php

namespace App\Console\Commands;

use App\Channel;
use App\Mail\ChannelIsRegistered;
use App\Mail\MonthlyReportMail;
use App\Mail\WelcomeToPodmytube;
use App\Media;
use App\Plan;
use App\Subscription;
use App\User;
use Carbon\Carbon;
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

    /** @var string $email email address */
    protected $email;

    /** @var int $emailIdToSend email id to be sent */
    protected $emailIdToSend;

    /** App\Subscription $subscription subscription model */
    protected $subscription;

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
            3 => ['label' => 'Monthly report for free plan.'],
            4 => [
                'label' =>
                    'Monthly report for paying user (no upgrade message) .',
            ],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->checkEmail()) {
            $this->error("Email address {$this->email} is not valid !");
            return;
        }

        if (!$this->askUserWhatMailToSend()) {
            $this->error(
                'Only numbers between ' .
                    array_keys($this->availableEmails)[0] .
                    '-' .
                    array_keys($this->availableEmails)[
                        count($this->availableEmails) - 1
                    ] .
                    '  are accepted.'
            );
            return;
        }

        switch ($this->emailIdToSend) {
            case 1:
                $mailable = new WelcomeToPodmytube(User::first());
                break;
            case 2:
                $mailable = new ChannelIsRegistered(Channel::first());
                break;
            case 3: // monthly report with upgrade message
                $this->createFakeChannelWithVideos(Plan::FREE_PLAN_ID, 3);
                $mailable = new MonthlyReportMail($this->subscription->channel);
                break;
            case 4: // monthly with upgrade message
                $this->createFakeChannelWithVideos(Plan::WEEKLY_PLAN_ID, 5);
                $mailable = new MonthlyReportMail($this->subscription->channel);
                break;
        }
        Mail::to($this->email)->send($mailable);

        /** cleaning */
        $this->cleaning();

        #$class = ChannelIsRegistered::class;
        #Mail::to($email)->send(new $class($channel->user, $channel));

        $this->comment(
            'Email {' .
                $this->availableEmails[$this->emailIdToSend]['label'] .
                "} has been sent to {$this->email}."
        );
    }

    protected function cleaning()
    {
        if ($this->subscription) {
            $this->subscription->channel->user->delete();
        }
    }

    protected function createFakeChannelWithVideos(int $planId, int $nbVideos)
    {
        $this->subscription = factory(Subscription::class)->create([
            'plan_id' => $planId,
        ]);
        factory(Media::class, $nbVideos)->create([
            'channel_id' => $this->subscription->channel->channel_id,
            'published_at' => Carbon::now()->subMonth(),
        ]);
    }

    /**
     * check email.
     *
     * @return bool
     */
    protected function checkEmail(): bool
    {
        if (
            filter_var(
                $this->email = $this->argument('emailAddress'),
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            return false;
        }
        return true;
    }

    /**
     * ask what kind of mail to send
     *
     * @return bool
     */
    protected function askUserWhatMailToSend(): bool
    {
        $this->comment('Here are the mails you can send :');
        foreach ($this->availableEmails as $id => $availableEmail) {
            $this->info($id . ' - ' . $availableEmail['label']);
        }

        $this->emailIdToSend = $this->ask('Which one do you want to send ?');
        if (
            !in_array($this->emailIdToSend, array_keys($this->availableEmails))
        ) {
            return false;
        }
        return true;
    }
}
