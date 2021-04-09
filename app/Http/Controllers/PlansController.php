<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PlansController extends Controller
{
    /**
     * Show the available plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Channel $channel)
    {
        $isYearly = $request->get('yearly') == 1 ? true : false;

        Stripe::setApiKey(config('services.stripe.secret'));

        $plans = Plan::bySlugsAndBillingFrequency(['starter', 'professional', 'business'], $isYearly);

        $stripeIdColumn = App::environment('production') ? 'stripe_live_id' : 'stripe_test_id';

        /**
         * foreach plan create a session id that will be associated with plan
         */
        $plans->map(function ($plan) use ($channel, $stripeIdColumn) {
            $stripeSessionParams = [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        // it s a price ID not the price in €
                        'price' => $plan->stripePlan->first()->$stripeIdColumn,
                        'quantity' => 1,
                    ],
                ],
                'subscription_data' => [
                    'trial_period_days' => 30,
                ],
                'mode' => 'subscription',
                'success_url' => config('app.url') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.url') . '/cancel',
                'metadata' => ['channel_id' => $channel->channel_id],
            ];

            if ($channel->user->stripe_id !== null) {
                $stripeSessionParams['customer'] = $channel->user->stripe_id;
            } else {
                $stripeSessionParams['customer_email'] = $channel->user->email;
            }

            $plan->stripeSession = Session::create($stripeSessionParams);
        });

        return view('plans.index', compact('channel', 'plans', 'isYearly'));
    }
}
