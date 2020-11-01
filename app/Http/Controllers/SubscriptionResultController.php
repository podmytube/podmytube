<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class SubscriptionResultController extends Controller
{
    public function success(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $stripe->checkout->sessions->retrieve($request->get('session_id'), []);
        return view('subscription.success');
    }

    public function failure(Request $request)
    {
        return view('subscription.failure');
    }
}
