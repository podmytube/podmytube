<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class SubscriptionResultController extends Controller
{
    public function success(Request $request)
    {
        $stripe = new StripeClient(config('app.stripe_secret'));
        $stripe->checkout->sessions->retrieve($request->get('session_id'), []);

        return view('subscription.success');
    }

    public function failure()
    {
        return view('subscription.failure');
    }
}
