<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Plan;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PlansController extends Controller
{
    /**
     * Show the available plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $plans = Plan::bySlugs(['starter', 'profesional', 'business']);
        dd($plans);

        /**
         * foreach plan create a session id that will be associated with plan
         */
        $plans->map(function ($plan) use ($channel) {
            $stripeSessionParams = [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $plan->stripe_id,
                        'quantity' => 1,
                        'trial_period_days' => 30,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => config('app.url') .
                    '/success?session_id={CHECKOUT_SESSION_ID}',
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

        return view('plans.index', compact('channel', 'plans'));
    }
}
