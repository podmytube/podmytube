<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Plan;
use App\Services\PlanService;

class PlansController extends Controller
{
    
    const _WEEKLY = Plan::_WEEKLY_PLAN_ID;
    const _DAILY = Plan::_DAILY_PLAN_ID;

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
                env('APP_ENV') == 'prod' ? true : false
            );
        } catch (\Exception $e) {
            session()->flash('message', __('messages.a_problem_occur'));
            session()->flash('messageClass', 'alert-danger');            
        }
        $weekly = self::_WEEKLY;
        $daily = self::_DAILY;
        
        return view('plans.index', compact('channel', 'stripePlans', 'weekly', 'daily'));
    }
}
