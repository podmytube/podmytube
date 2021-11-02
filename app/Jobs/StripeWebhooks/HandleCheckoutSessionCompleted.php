<?php

declare(strict_types=1);

namespace App\Jobs\StripeWebhooks;

use App\Channel;
use App\Exceptions\CannotIdentifyUserFromStripeException;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyCustomerReceivedFromStripeException;
use App\Exceptions\EmptyPlanReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Exceptions\UnknownStripePlanReceivedFromStripeException;
use App\StripePlan;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;

class HandleCheckoutSessionCompleted implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const ERROR_MESSAGE_EMPTY_CUSTOMER = 'No customer in json.';
    public const ERROR_MESSAGE_CUSTOMER_NOT_FOUND = 'Customer not found.';
    public const ERROR_MESSAGE_EMPTY_CHANNEL = 'No channel in json.';
    public const ERROR_MESSAGE_CHANNEL_NOT_FOUND = 'Channel not found.';
    public const ERROR_MESSAGE_USER_IS_NOT_OWNER = 'User is not the owner.';
    public const ERROR_MESSAGE_EMPTY_SUBSCRIPTION = 'No subscription in json.';
    public const ERROR_MESSAGE_SUBSCRIPTION_NOT_FOUND = 'Suscription not found.';
    public const ERROR_MESSAGE_NO_ERROR = 'ok';

    public WebhookCall $webhookCall;

    protected User $user;
    protected Channel $channel;
    protected StripePlan $stripePlan;

    /** var int $endsAt contain a timestamp returned by stripe for subscription ending */
    protected $endsAt;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle(): int
    {
        $user = $this->getUserFromJson();
        if ($user === null) {
            throw new CannotIdentifyUserFromStripeException(self::ERROR_MESSAGE_CUSTOMER_NOT_FOUND);
        }

        $channel = $this->getChannelFromJson($user);

        // check subscription received from stripe
        $this->checkSubscription();

        // update subscription on Pod side
        $this->updateSubscription();

        return 0;
    }

    protected function getUserFromJson(): ?User
    {
        $customerStripeId = $this->customerIdFromJson();
        $email = $this->customerEmailFromJson();

        if ($customerStripeId === null && $email === null) {
            throw new EmptyCustomerReceivedFromStripeException(self::ERROR_MESSAGE_EMPTY_CUSTOMER);
        }

        if ($customerStripeId !== null) {
            return User::byStripeId($customerStripeId);
        }

        if ($email !== null) {
            return User::byEmail($email);
        }

        return null;
    }

    protected function getChannelFromJson(User $user): Channel
    {
        // checking channel id in json
        $channelId = $this->channelIdFromJson();
        if ($channelId === null) {
            throw new EmptyChannelIdReceivedFromStripeException(self::ERROR_MESSAGE_EMPTY_CHANNEL);
        }

        // obtaining channel
        $channel = Channel::byChannelId($channelId);
        if ($channel === null) {
            throw new UnknownChannelIdReceivedFromStripeException(self::ERROR_MESSAGE_CHANNEL_NOT_FOUND);
        }

        // channel should belongs to the user
        if ($channel->user->id() !== $user->id()) {
            throw new ChannelOwnerMismatchingStripeException(self::ERROR_MESSAGE_USER_IS_NOT_OWNER);
        }

        return $channel;
    }

    /**
     * extract email if any from json.
     */
    protected function customerEmailFromJson(): ?string
    {
        return $this->webhookCall->payload['data']['object']['customer_email'] ?? null;
    }

    /**
     * extract customer stripe id from json.
     */
    protected function customerIdFromJson(): ?string
    {
        return $this->webhookCall->payload['data']['object']['customer'] ?? null;
    }

    /**
     * extract channel_id from json.
     */
    protected function channelIdFromJson(): ?string
    {
        return $this->webhookCall->payload['data']['object']['metadata']['channel_id'] ?? null;
    }

    protected function subscriptionIdFromJson(): ?string
    {
        return $this->webhookCall->payload['data']['object']['subscription'] ?? null;
    }

    /**
     * getting subscription user has chosen.
     */
    protected function checkSubscription()
    {
        $subscriptionId = $this->subscriptionIdFromJson();
        if ($subscriptionId === null) {
            throw new EmptySubscriptionReceivedFromStripeException(HandleCheckoutSessionCompleted::ERROR_MESSAGE_EMPTY_SUBSCRIPTION);
        }

        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $subscription = $stripe->plans->retrieve(
                $subscriptionId,
                []
            );
        } catch (Exception $exception) {
            throw new InvalidSubscriptionReceivedFromStripeException(HandleCheckoutSessionCompleted::ERROR_MESSAGE_SUBSCRIPTION_NOT_FOUND);
        }

        $currentPeriodEnd = $subscription['current_period_end'] ?? null;
        if ($currentPeriodEnd !== null) {
            $this->endsAt = Carbon::createFromTimestamp($currentPeriodEnd);
        }

        $stripePlanId = $subscription['items']['data'][0]['plan']['id'] ?? null;
        if ($stripePlanId === null) {
            throw new EmptyPlanReceivedFromStripeException('Plan id received from stripe is empty.');
        }

        $this->stripePlan = StripePlan::where('stripe_id', '=', $stripePlanId)->first();
        if ($this->stripePlan === null) {
            throw new UnknownStripePlanReceivedFromStripeException('Stripe plan id received from stripe is unknown.');
        }

        return true;
    }

    protected function updateSubscription(): void
    {
        $actualSubscription = Subscription::where('channel_id', '=', $this->channel->channel_id)->first();
        $actualSubscription->plan_id = $this->stripePlan->plan_id;
        $actualSubscription->ends_at = $this->endsAt;
        $actualSubscription->save();
    }
}
