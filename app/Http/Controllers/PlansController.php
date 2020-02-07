<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Channel;
use App\StripePlan;
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
                [
                    Plan::_WEEKLY_PLAN_ID,
                    Plan::_DAILY_PLAN_ID,
                ],
                env('APP_ENV') == 'production' ? true : false
            );
        } catch (\Exception $e) {
            session()->flash('message', __('messages.a_problem_occur'));
            session()->flash('messageClass', 'alert-danger');
        }
        $weekly = Plan::_WEEKLY_PLAN_ID;
        $daily = Plan::_DAILY_PLAN_ID;

        /* $plans = Plan::whereIn('id', [Plan::_DAILY_PLAN_ID, Plan::_WEEKLY_PLAN_ID])
            ->orderBy('price', 'ASC')
            ->get(); */
        return view('plans.index', compact('channel', 'stripePlans', 'weekly', 'daily'/* , $plans */));
    }
}
