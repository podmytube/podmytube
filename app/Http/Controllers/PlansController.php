<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    /**
     * Show the available plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Channel $channel)
    {
        $this->authorize($channel);

        $isYearly = $request->get('yearly') === '1' ? true : false;

        $plans = Plan::bySlugsAndBillingFrequency(['starter', 'professional', 'business'], $isYearly);

        // foreach plan create a session id that will be associated with plan
        $plans->map(function (Plan $plan) use ($channel): void {
            $plan->addStripeSessionForChannel($channel);
        });

        $buttonLabel = 'Upgrade';

        return view('plans.index')->with([
            'routeName' => 'plans.index',
            'channel' => $channel,
            'plans' => $plans,
            'isYearly' => $isYearly,
            'buttonLabel' => $buttonLabel,
        ]);
    }
}
