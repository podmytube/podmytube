<?php

namespace App\Interfaces;

/**
 * QuotasConsumer is consuming an api with qeries.
 * It must be able to tell which apis have been used.
 */
interface QuotasConsumer
{
    public function queriesUsed(): array;
}
