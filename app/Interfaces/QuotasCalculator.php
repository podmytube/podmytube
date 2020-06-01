<?php

namespace App\Interfaces;

/**
 * QuotasCalculator must be able to calculate how much quotas have been used
 * from many urls.
 */
interface QuotasCalculator
{
    /**
     * should return quota consumption by key used
     * IE : ['key1' => 7, 'key2' => 3]
     */
    public function quotaConsumed(): array;
}
