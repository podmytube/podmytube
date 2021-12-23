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
use App\Exceptions\SubscriptionUpdateFailureException;
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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\StripeClient;

class HandleCheckoutSessionCompleted implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const LOG_PREFIX = 'Stripe Subscription - checkout.session.completed ';
    public const ERROR_MESSAGE_NO_ERROR = 'Subscription success.';

    public WebhookCall $webhookCall;

    protected User $user;
    protected Channel $channel;
    protected ?StripePlan $stripePlan;

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
            $exception = new CannotIdentifyUserFromStripeException();
            $exception->addInformations('customerId from stripe : ' . $this->customerIdFromJson());
            $exception->addInformations('email from stripe : ' . $this->customerEmailFromJson());

            throw $exception;
        }

        $this->channel = $this->getChannelFromJson();

        // channel should belongs to the user
        if ($this->channel->user->id() !== $user->id()) {
            throw new ChannelOwnerMismatchingStripeException();
        }

        $stripeClient = new StripeClient(config('app.stripe_secret'));
        // check subscription received from stripe
        $this->checkSubscription($stripeClient);

        // update subscription on Pod side
        $this->updateSubscription();

        return 0;
    }

    protected function getUserFromJson(): ?User
    {
        $customerStripeId = $this->customerIdFromJson();
        $email = $this->customerEmailFromJson();
        Log::debug(self::LOG_PREFIX . "customerStripeId : {$customerStripeId} --- email : {$email}");

        if ($customerStripeId === null && $email === null) {
            throw new EmptyCustomerReceivedFromStripeException();
        }

        // we are looking for the stripe customer id.
        if ($customerStripeId !== null) {
            $user = User::byStripeId($customerStripeId);
            if ($user !== null) {
                return $user;
            }
        }

        // else we are looking for customer email
        if ($email !== null) {
            return User::byEmail($email);
        }

        return null;
    }

    protected function getChannelFromJson(): Channel
    {
        // checking channel id in json
        $channelId = $this->channelIdFromJson();
        if ($channelId === null) {
            throw new EmptyChannelIdReceivedFromStripeException();
        }

        // obtaining channel
        $channel = Channel::byChannelId($channelId);
        if ($channel === null) {
            $exception = new UnknownChannelIdReceivedFromStripeException();
            $exception->addInformations('channel_id from stripe : ' . $channelId);

            throw $exception;
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
    protected function checkSubscription(StripeClient $stripeClient)
    {
        $subscriptionId = $this->subscriptionIdFromJson();
        if ($subscriptionId === null) {
            throw new EmptySubscriptionReceivedFromStripeException();
        }

        try {
            $subscription = $stripeClient->subscriptions->retrieve(
                $subscriptionId,
                []
            );
        } catch (Exception $thrownException) {
            $exception = new InvalidSubscriptionReceivedFromStripeException();
            $exception->addInformations('Received subscription : ' . $subscriptionId);
            $exception->addInformations($thrownException->getMessage());

            throw $exception;
        }

        $currentPeriodEnd = $subscription['current_period_end'] ?? null;
        if ($currentPeriodEnd !== null) {
            $this->endsAt = Carbon::createFromTimestamp($currentPeriodEnd);
        }

        $stripePlanId = $subscription->plan->id ?? null;
        Log::debug(self::LOG_PREFIX . "for channel {$this->channel->channelId()} with stripePlanId : {$stripePlanId}");
        if ($stripePlanId === null) {
            throw new EmptyPlanReceivedFromStripeException();
        }

        $this->stripePlan = StripePlan::byStripeId($stripePlanId, App::isProduction());
        if ($this->stripePlan === null) {
            $exception = new UnknownStripePlanReceivedFromStripeException();
            $exception->addInformations("Stripe plan id received from stripe : {$stripePlanId}");

            throw $exception;
        }
        Log::debug(self::LOG_PREFIX . "for channel/plan {$this->channel->channelId()}/{$stripePlanId} success !🎉");

        return true;
    }

    protected function updateSubscription(): void
    {
        try {
            $actualSubscription = Subscription::where('channel_id', '=', $this->channel->channel_id)->first();
            $actualSubscription->plan_id = $this->stripePlan->plan_id;
            $actualSubscription->ends_at = $this->endsAt;
            $actualSubscription->save();
        } catch (Exception $thrownException) {
            $exception = new SubscriptionUpdateFailureException();
            $exception->addInformations("channel_id : {$this->channel->channelId()}");
            $exception->addInformations("stripe_plan_id : {$this->stripePlan->plan_id}");

            throw $exception;
        }
    }
}
