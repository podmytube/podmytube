<?php

namespace App\Interfaces;

interface QuotasConsumer
{
    public function quotasUsed(): int;
}
