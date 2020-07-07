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

        Stripe::setApiKey(env("STRIPE_SECRET"));

        $plans = Plan::byIds([Plan::WEEKLY_PLAN_ID, Plan::DAILY_PLAN_ID]);

        /**
         * foreach plan create a session id that will be associated with plan
         */
        $plans->map(function ($plan) use ($channel) {
            $plan->stripeSession =
                Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $plan->stripe_id,
                        'quantity' => 1,
                    ]],
                    'mode' => 'subscription',
                    'success_url' => env("APP_URL") . '/success?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => env("APP_URL") . '/cancel',
                    "metadata" => ["channel_id" => $channel->channel_id],
                ]);
        });

        return view('plans.index', compact('channel', 'plans'));
    }
}
