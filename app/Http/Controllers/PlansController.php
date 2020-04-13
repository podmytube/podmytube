<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Plan;
use App\Services\PlanService;

class PlansController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the available plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        try {
            $stripePlans = PlanService::getStripePlans(
                [Plan::WEEKLY_PLAN_ID, Plan::DAILY_PLAN_ID],
                env('APP_ENV') === 'production' ? true : false
            );
        } catch (\Exception $exception) {
            session()->flash('message', __('messages.a_problem_occur'));
            session()->flash('messageClass', 'alert-danger');
        }
        $weekly = Plan::WEEKLY_PLAN_ID;
        $daily = Plan::DAILY_PLAN_ID;

        return view(
            'plans.index',
            compact('channel', 'stripePlans', 'weekly', 'daily' /* , $plans */)
        );
    }
}
