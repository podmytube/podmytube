<?php

namespace App\Jobs\StripeWebhooks;

use App\Channel;
use App\Exceptions\CannotIdentifyUserFromStripeException;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyPlanReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownEmailReceivedFromStripeException;
use App\Exceptions\UnknownStripePlanReceivedFromStripeException;
use App\StripePlan;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Models\WebhookCall;

class HandleCheckoutSessionCompleted implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** var \App\User $user */
    protected $user;

    /** var \App\Channel $channel */
    protected $channel;

    /** var \App\StripePlan $stripePlan */
    protected $stripePlan;

    /** var int $endsAt contain a timestamp returned by stripe for subscription ending */
    protected $endsAt;

    /** @var \Spatie\WebhookClient\Models\WebhookCall */
    public $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        Log::info("checkout session completed");
        Log::debug(print_r($this->webhookCall->payload, true));

        /**
         * checking user received from stripe
         */
        $userFound = $this->checkStripeUser();
        if ($userFound === false) {
            throw new CannotIdentifyUserFromStripeException("User not found. customer id : " . $this->customerId() . "--- email : " . $this->customerEmail());
        }

        /**
         * checking channel id received
         */
        $this->checkStripeChannelId();

        /**
         * check subscription received from stripe
         */
        $this->checkSubscription();

        /**
         * update subscription on Pod side
         */
        $this->updateSubscription();
    }

    protected function checkStripeUser(): bool
    {
        $customerStripeId = $this->customerId();
        if ($customerStripeId !== null && $this->obtainUserFromStripeId()) {
            return true;
        }

        $customerEmail = $this->customerEmail();
        if ($customerEmail !== null &&  $this->obtainUserFromEmail()) {
            return true;
        }

        return false;
    }

    /**
     * will affect stripe customer id to user.
     */
    protected function obtainUserFromEmail(): bool
    {
        $customerEmail = $this->customerEmail();
        $customerStripeId = $this->customerId();

        /**
         * getting user from his email address
         */
        $this->user = User::where('email', '=', $customerEmail)->first();
        if ($this->user === null) {
            throw new UnknownEmailReceivedFromStripeException("Email address $customerEmail is unknown.");
        }

        /**
         * associating stripe id
         */
        $this->user->stripe_id = $customerStripeId;
        $this->user->save();
        return true;
    }

    protected function obtainUserFromStripeId(): bool
    {
        $customerStripeId = $this->customerId();
        $this->user = User::where('stripe_id', '=', $customerStripeId)->first();
        if ($this->user === null) {
            return false;
        }
        return true;
    }

    /**
     * extract email if any from json.
     */
    protected function customerEmail()
    {
        return $this->webhookCall->payload['data']['object']['customer_email'] ?? null;
    }

    /**
     * extract customer stripe id from json.
     */
    protected function customerId()
    {
        return $this->webhookCall->payload['data']['object']['customer'] ?? null;
    }

    /**
     * will check channel id is valid.
     */
    protected function checkStripeChannelId()
    {
        /**
         * channel id should be returned within stripe metadata
         */
        $channelId = $this->webhookCall->payload['data']['object']['metadata']['channel_id'] ?? null;
        if ($channelId === null) {
            throw new EmptyChannelIdReceivedFromStripeException("Channel id received from stripe is empty.");
        }

        /**
         * channel id should exists
         */
        $this->channel = Channel::findOrFail($channelId);

        /**
         * channel should belongs to the user
         */
        if ($this->channel->user->id() !== $this->user->id()) {
            throw new ChannelOwnerMismatchingStripeException("Channel {$this->channel->channel_name} do not belongs to {$this->user->id()}.");
        }
    }

    /**
     * getting subscription user has chosen.
     */
    protected function checkSubscription()
    {
        $subscriptionId = $this->webhookCall->payload['data']['object']['subscription'] ?? null;
        if ($subscriptionId === null) {
            throw new EmptySubscriptionReceivedFromStripeException("Subscription id received from stripe is empty.");
        }

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $subscription = $stripe->subscriptions->retrieve(
            $subscriptionId,
            []
        );
        $currentPeriodEnd = $subscription['current_period_end'] ?? null;
        if ($currentPeriodEnd !== null) {
            $this->endsAt = Carbon::createFromTimestamp($currentPeriodEnd);
        }

        $stripePlanId = $subscription['items']['data'][0]['plan']['id'] ?? null;
        if ($stripePlanId === null) {
            throw new EmptyPlanReceivedFromStripeException("Plan id received from stripe is empty.");
        }

        $this->stripePlan = StripePlan::where('stripe_id', '=', $stripePlanId)->first();
        if ($this->stripePlan === null) {
            throw new UnknownStripePlanReceivedFromStripeException("Stripe plan id received from stripe is unknown.");
        }

        return true;
    }

    protected function updateSubscription()
    {
        $actualSubscription = Subscription::where('channel_id', '=', $this->channel->channel_id)->first();
        $actualSubscription->plan_id = $this->stripePlan->plan_id;
        $actualSubscription->ends_at = $this->endsAt;
        $actualSubscription->save();
    }
}
