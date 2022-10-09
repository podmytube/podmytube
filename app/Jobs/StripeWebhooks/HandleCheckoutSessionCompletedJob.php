<?php

declare(strict_types=1);

namespace App\Jobs\StripeWebhooks;

use App\Events\ChannelRegisteredEvent;
use App\Exceptions\CannotIdentifyUserFromStripeException;
use App\Exceptions\ChannelOwnerMismatchingStripeException;
use App\Exceptions\EmptyChannelIdReceivedFromStripeException;
use App\Exceptions\EmptyCustomerReceivedFromStripeException;
use App\Exceptions\EmptyPlanReceivedFromStripeException;
use App\Exceptions\EmptySubscriptionReceivedFromStripeException;
use App\Exceptions\InvalidCustomerEmailReceivedFromStripeException;
use App\Exceptions\InvalidSubscriptionReceivedFromStripeException;
use App\Exceptions\SubscriptionUpdateFailureException;
use App\Exceptions\UnknownChannelIdReceivedFromStripeException;
use App\Exceptions\UnknownStripePlanReceivedFromStripeException;
use App\Models\Channel;
use App\Models\StripePlan;
use App\Models\Subscription;
use App\Models\User;
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

class HandleCheckoutSessionCompletedJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const LOG_PREFIX = 'Stripe Subscription - checkout.session.completed ';
    public const ERROR_MESSAGE_NO_ERROR = 'Subscription success.';

    public WebhookCall $webhookCall;

    protected ?User $user;
    protected Channel $channel;
    protected ?StripePlan $stripePlan;
    protected StripeClient $stripeClient;
    protected ?Carbon $endsAt;
    protected ?string $customerStripeId;

    public function __construct(WebhookCall $webhookCall, ?StripeClient $stripeClient = null)
    {
        $this->webhookCall = $webhookCall;

        // this part is required because I want being able to mock the stripeClient
        if ($stripeClient === null) {
            $this->stripeClient = new StripeClient(config('app.stripe_secret'));
        } else {
            $this->stripeClient = $stripeClient;
        }
    }

    public function handle(): int
    {
        Log::debug(self::LOG_PREFIX . 'started');
        $this->user = $this->obtainUserFromJson();
        if ($this->user === null) {
            $exception = new CannotIdentifyUserFromStripeException();
            $exception->addInformations('customerId from stripe : ' . $this->customerIdFromJson());
            $exception->addInformations('email from stripe : ' . $this->customerEmailFromJson());

            throw $exception;
        }

        Log::debug(self::LOG_PREFIX . 'user obtained');
        $this->channel = $this->getChannelFromJson();

        // channel should belongs to the user
        if ($this->channel->user->id() !== $this->user->id()) {
            throw new ChannelOwnerMismatchingStripeException();
        }

        Log::debug(self::LOG_PREFIX . 'channel obtained and ownership verified');

        // check subscription received from stripe
        $this->checkSubscription();

        // update subscription on Pod side
        $this->updateSubscription();

        // update user with stripe customer id.
        $this->user->update(['stripe_id' => $this->customerStripeId]);

        // finally update channel active status
        $this->channel->update(['active' => true]);

        ChannelRegisteredEvent::dispatch($this->channel);

        return 0;
    }

    protected function obtainUserFromJson(): ?User
    {
        $this->customerStripeId = $this->customerIdFromJson();
        $email = $this->customerEmailFromJson();
        Log::debug(self::LOG_PREFIX . "customerStripeId : {$this->customerStripeId} --- email : {$email}");

        if ($this->customerStripeId === null && $email === null) {
            throw new EmptyCustomerReceivedFromStripeException();
        }

        // we are looking for the stripe customer id.
        if ($this->customerStripeId !== null) {
            $user = User::byStripeId($this->customerStripeId);
            if ($user !== null) {
                return $user;
            }
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $exception = new InvalidCustomerEmailReceivedFromStripeException();
            $exception->addInformations("Email : {$email}");

            throw $exception;
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
    protected function checkSubscription()
    {
        Log::debug(self::LOG_PREFIX . __FUNCTION__);

        $subscriptionId = $this->subscriptionIdFromJson();
        if ($subscriptionId === null) {
            throw new EmptySubscriptionReceivedFromStripeException();
        }

        Log::debug(self::LOG_PREFIX . "subscriptionId : {$subscriptionId}");

        try {
            $subscription = $this->stripeClient->subscriptions->retrieve(
                $subscriptionId,
                []
            );
        } catch (Exception $thrownException) {
            Log::debug(self::LOG_PREFIX . "subscriptionId : {$subscriptionId} failed");
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
            Subscription::query()
                ->updateOrCreate(
                    ['channel_id' => $this->channel->channelId()],
                    [
                        'channel_id' => $this->channel->channelId(),
                        'plan_id' => $this->stripePlan->plan_id,
                        'ends_at' => $this->endsAt,
                    ]
                )
            ;
        } catch (Exception $thrownException) {
            $exception = new SubscriptionUpdateFailureException();
            $exception->addInformations("channel_id : {$this->channel->channelId()}");
            $exception->addInformations("stripe_plan_id : {$this->stripePlan->plan_id}");
            $exception->addInformations("error : {$thrownException->getMessage()}");

            throw $exception;
        }
    }
}
