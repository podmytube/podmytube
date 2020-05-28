<?php

namespace App\Interfaces;

interface QuotasCalculator
{
    public function quotas(): int;
    public function addQuotaConsumer(QuotasConsumer $quotasConsumer);
}
