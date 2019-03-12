<?php

namespace App\Services;

use App\Plan;

class PlanService
{

    public static function getStripePlan(int $plan_id, bool $is_live = false)
    {
        try {
            return Plan::find($plan_id)->stripePlan->where('is_live',$is_live)->stripe_id;
        } catch (\Exception $e) {
            throw $e;
        }
        return false;
    }

}
