<?php

namespace App\Interfaces;

/**
 * QuotasCalculator must be able to calculate how much quotas have been used
 * from many urls.
 */
interface QuotasCalculator
{
    public function quotaConsumed(): int;
}
