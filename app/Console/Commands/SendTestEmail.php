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
    protected const DEFAULT_EMAIL = 'frederick@podmytube.com';

    /** @var string $signature The name and signature of the console command. */
    protected $signature = 'email:test {email=frederick@podmytube.com : email address to send email to}';

    /** @var string $description The console command description. */
    protected $description = 'This command is allowing me to send test email to myself (by default) and check if everything is fine.';

    /** @var int $emailIdToSend email id to be sent */
    protected $emailIdToSend;

    /** @var \App\User $user */
    protected $user;

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Subscription $subscription subscription model */
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
            4 => ['label' => 'Monthly report for paying user (no upgrade message) .'],
            //5 => ['label' => 'Newsletter.'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->askUserWhatMailToSend()) {
            return;
        }

        // handle user with email
        $this->fakeUser();

        // handle user channel
        $this->fakeChannel();

        switch ($this->emailIdToSend) {
            case 1:
                $mailable = new WelcomeToPodmytube($this->user);
                break;
            case 2:
                $mailable = new ChannelIsRegistered($this->channel);
                break;
            case 3: // monthly report with upgrade message
                $this->fakeSubscription(Plan::bySlug('forever_free'));
                $mailable = new MonthlyReportMail($this->subscription->channel);
                break;
            case 4: // monthly report with upgrade message
                $this->fakeSubscription(Plan::bySlug('weekly_youtuber'));
                $mailable = new MonthlyReportMail($this->subscription->channel);
                break;
            /* case 5:
                $mailable = new Newsletter($this->user);
                break; */
        }

        // send it to me with the right locale
        Mail::to($this->user)->queue($mailable);

        $this->comment(
            'Email "' .
                $this->availableEmails[$this->emailIdToSend]['label'] .
                "\" has been queued to be sent to {{$this->user->email}}."
        );
    }

    protected function fakeUser()
    {
        // if no user exists
        $this->user = User::byEmail($this->argument('email'));
        if ($this->user === null) {
            $this->user = factory(User::class)->create(['email' => $this->argument('email')]);
        }
    }

    protected function fakeChannel()
    {
        // if this user has no channel
        if (!$this->user->channels->count()) {
            $this->channel = factory(Channel::class)->create([
                'user_id' => $this->user->user_id,
            ]);
            return true;
        }
        $this->channel = $this->user->channels->first();
    }

    protected function fakeSubscription(Plan $plan)
    {
        if ($this->channel->subscription === null) {
            $this->subscription = factory(Subscription::class)->create([
                'plan_id' => $plan->id,
                'channel_id' => $this->channel->channel_id,
            ]);
            $nbMediasToCreate = $plan->nb_episodes_per_month + 1;
            factory(Media::class, $nbMediasToCreate)->create([
                'channel_id' => $this->channel->channel_id,
                'published_at' => Carbon::now()->subMonth(),
            ]);
            return true;
        }
        $this->subscription = $this->channel->subscription;
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
