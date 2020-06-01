<?php

namespace App\Services;

use App\Plan;

class PlanService
{
    /**
     * This function will retrieve the plan to apply on the "Subscribe page" according to the env.
     *
     * @param int $planId
     * @param bool $isLive
     *
     * @return void
     */
    public static function getStripePlans(array $plansId, bool $isLive = true)
    {
        $results = [];
        try {
            foreach ($plansId as $planId) {
                $results[$planId] = self::getStripePlan($planId, $isLive);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
        return $results;
    }

    /**
     * This function will retrieve the plan to apply on the "Subscribe page" according to the env.
     *
     * @param int $planId
     * @param bool $isLive
     *
     * @return void
     */
    public static function getStripePlan(int $planId, bool $isLive = true)
    {
        try {
            return Plan::find($planId)
                ->stripePlan->where('is_live', $isLive)
                ->first()->stripe_id;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
