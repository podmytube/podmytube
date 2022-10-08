<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\StripeSessionCreationFailureException;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Stripe\Checkout\Session;
use Stripe\Stripe;

/**
 * @property int          $billing_yearly;
 * @property string       $name;
 * @property int          $nb_episodes_per_month;
 * @property int          $price;
 * @property string       $slug;
 * @property Subscription $subscription;
 */
class Plan extends Model
{
    use HasFactory;

    public const FREE_PLAN_ID = 1;
    public const EARLY_PLAN_ID = 2;
    public const PROMO_MONTHLY_PLAN_ID = 3; // old 6€/month plan
    public const PROMO_YEARLY_PLAN_ID = 4; // old 66€/month plan
    public const WEEKLY_PLAN_ID = 5;
    public const DAILY_PLAN_ID = 6;
    public const DEFAULT_PLAN_ID = self::FREE_PLAN_ID;
    public const WEEKLY_PLAN_PROMO_ID = 8;
    public const DAILY_PLAN_PROMO_ID = 9;

    protected int $id;
    protected string $name;
    protected string $slug;
    protected int $price;
    protected int $billing_yearly; // @todo remove this
    protected int $nb_episodes_per_month;
    protected Carbon $created_at;
    protected Carbon $updated_at;
    protected ?Session $stripeSession = null;

    protected $casts = [
        'price' => 'integer',
        'nb_episodes_per_month' => 'integer',
    ];

    /**
     * One plan may be subscribed by many channels.
     *
     * @return object App\Models\Subscription
     */
    public function subscriptions()
    {
        return $this->HasMany(Subscription::class);
    }

    /**
     * @return object App\Models\StripePlan
     */
    public function stripePlans()
    {
        return $this->HasMany(StripePlan::class);
    }

    public function scopeFree(Builder $query)
    {
        return $query->where('id', '=', self::FREE_PLAN_ID);
    }

    public function scopePaying(Builder $query)
    {
        return $query->whereNotIn('id', [
            self::FREE_PLAN_ID,
            self::EARLY_PLAN_ID,
        ]);
    }

    public static function byIds(array $planIds)
    {
        return static::whereIn('plans.id', $planIds)
            ->join('stripe_plans', function ($join): void {
                $join
                    ->on('stripe_plans.plan_id', '=', 'plans.id')
                    ->where(
                        'stripe_plans.is_live',
                        '=',
                        env('APP_ENV') === 'production' ? true : false
                    )
                ;
            })
            ->orderBy('price', 'ASC')
            ->get()
        ;
    }

    public function scopeSlug(Builder $query, string $slug)
    {
        return $query->where('slug', '=', $slug);
    }

    public static function bySlug(string $slug)
    {
        return (new static())->slug($slug)->first();
    }

    public function scopeSlugs(Builder $query, array $slugs)
    {
        return $query->whereIn('slug', $slugs);
    }

    public static function bySlugs(array $slugs): ?Collection
    {
        $results = (new static())->slugs($slugs)->get();
        if (!$results->count()) {
            return null;
        }

        return $results;
    }

    public function scopeWithYearlyStripePlans(Builder $query)
    {
        return $query->whereHas('stripePlans', function (Builder $query) {
            return $query->where('is_yearly', '=', true);
        });
    }

    public static function onlyYearly(): ?Collection
    {
        $results = (new static())->withYearlyStripePlans()->get();
        if (!$results->count()) {
            return null;
        }

        return $results;
    }

    public static function bySlugsAndBillingFrequency(array $slugs, ?bool $isYearly = true): ?Collection
    {
        if (!count($slugs)) {
            throw new InvalidArgumentException('You should give some slugs to get plans');
        }

        return Plan::with(
            [
                'stripePlans' => function ($query) use ($isYearly): void {
                    $query->where('is_yearly', '=', $isYearly);
                },
            ]
        )
            ->slugs($slugs)
            ->get()
        ;
    }

    /**
     * add some stripe session data to be sent whith stripe checkout.
     *
     * @throws StripeSessionCreationFailureException
     */
    public function addStripeSessionForChannel(Channel $channel): self
    {
        try {
            $stripeIdColumn = App::environment('production') ? 'stripe_live_id' : 'stripe_test_id';
            $stripeSessionParams = $this->stripeSessionParams($channel, $stripeIdColumn);

            Stripe::setApiKey(config('services.stripe.secret'));
            $this->stripeSession = Session::create($stripeSessionParams);

            return $this;
        } catch (Exception $thrownException) {
            $exception = new StripeSessionCreationFailureException();
            $exception->addInformations($thrownException->getMessage());

            throw $exception;
        }
    }

    public function stripeSession(): ?Session
    {
        return $this->stripeSession;
    }

    protected function stripeSessionParams(Channel $channel, string $stripeIdColumn = 'stripe_live_id'): array
    {
        $stripeSessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    // it s a price ID not the price in €
                    'price' => $this->stripePlans->first()->{$stripeIdColumn},
                    'quantity' => 1,
                ],
            ],
            'subscription_data' => [
                'trial_period_days' => 30,
            ],
            'mode' => 'subscription',
            'success_url' => config('app.url') . '/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/cancel',
            'metadata' => [
                'channel_id' => $channel->channel_id,
            ],
        ];

        if ($channel->user->stripe_id !== null) {
            $stripeSessionParams['customer'] = $channel->user->stripe_id;
        } else {
            $stripeSessionParams['customer_email'] = $channel->user->email;
        }

        return $stripeSessionParams;
    }
}
