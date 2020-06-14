<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Plan;
use App\Services\PlanService;

class PlansController extends Controller
{
    /**
     * Show the available plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        $plans = Plan::byIds([
            Plan::WEEKLY_PLAN_PROMO_ID,
            Plan::DAILY_PLAN_PROMO_ID,
        ]);

        return view('plans.index', compact('channel', 'plans'));
    }
}
