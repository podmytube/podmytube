<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Jobs\SendChannelIsRegisteredEmailJob;
use App\Jobs\SendMonthlyReportEmailJob;
use App\Jobs\SendVerificationEmailJob;
use App\Jobs\SendWelcomeToPodmytubeEmailJob;
use App\Mail\ChannelHasReachedItsLimitsMail;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use InvalidArgumentException;

class TestSendingEmail extends Command
{
    use BaseCommand;

    /** @var string The name and signature of the console command. */
    protected $signature = 'test:email {emailIdToSend?}';

    /** @var string The console command description. */
    protected $description = 'This command is allowing me to send test email to myself (by default) and check if everything is fine.';

    protected User $user;

    protected Channel $channel;

    protected Subscription $subscription;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->availableEmails = [
            1 => ['label' => 'A new user has successfully registered.'],
            2 => ['label' => 'A new channel has been registered.'],
            3 => ['label' => 'Monthly report for free plan.'],
            4 => ['label' => 'Monthly report for paying user (no upgrade message) .'],
            5 => ['label' => 'TODO : create job for this mailable >>> Channel has reached its limits.'],
            6 => ['label' => 'Send verification email.'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->prologue();

        try {
            $this->emailIdToSend = $this->argument('emailIdToSend') ?? $this->askUserWhatMailToSend();
            if (!in_array($this->emailIdToSend, array_keys($this->availableEmails))) {
                throw new InvalidArgumentException("The template id ({$this->emailIdToSend}) you have chosen does not exists.");
            }

            // handle user with email
            $this->user = $this->fakeUser();

            // handle user channel
            $this->fakeChannel();

            switch ($this->emailIdToSend) {
                case 1:
                    SendWelcomeToPodmytubeEmailJob::dispatch($this->user);

                    break;

                case 2:
                    SendChannelIsRegisteredEmailJob::dispatch($this->channel);

                    break;

                case 3: // monthly report with upgrade message
                    $this->fakeSubscription(Plan::bySlug('forever_free'));
                    SendMonthlyReportEmailJob::dispatch($this->subscription->channel);

                    break;

                case 4: // monthly report with upgrade message
                    $this->fakeSubscription(Plan::bySlug('weekly_youtuber'));
                    SendMonthlyReportEmailJob::dispatch($this->subscription->channel);

                    break;
                    /* @todo create a job for this
                        case 5:
                            $mailable = new ChannelHasReachedItsLimitsMail($this->channel);

                            break; */

                case 6:
                    SendVerificationEmailJob::dispatch($this->user);

                    break;
            }

            $this->comment('email should have been dispatched/sent');
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        $this->epilogue();

        return 0;
    }

    protected function fakeUser(): User
    {
        // if no user exists
        return User::factory()->create();
    }

    protected function fakeChannel()
    {
        // if this user has no channel
        if (!$this->user->channels->count()) {
            $this->channel = Channel::factory()->create([
                'user_id' => $this->user->id,
            ]);

            return true;
        }
        $this->channel = $this->user->channels->first();
    }

    protected function fakeSubscription(Plan $plan)
    {
        if ($this->channel->subscription === null) {
            $this->subscription = Subscription::factory()->create([
                'plan_id' => $plan->id,
                'channel_id' => $this->channel->channel_id,
            ]);
            $nbMediasToCreate = $plan->nb_episodes_per_month + 1;
            Media::factory()->count($nbMediasToCreate)->create([
                'channel_id' => $this->channel->channel_id,
                'published_at' => Carbon::now()->subMonth(),
            ]);

            return true;
        }
        $this->subscription = $this->channel->subscription;
    }

    /**
     * ask what kind of mail to send.
     */
    protected function askUserWhatMailToSend(): int
    {
        $this->comment('Here are the mails you can send :');
        foreach ($this->availableEmails as $id => $availableEmail) {
            $this->info($id . ' - ' . $availableEmail['label']);
        }

        return intval($this->ask('Which one do you want to send ?'));
    }
}
