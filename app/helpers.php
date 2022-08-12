<?php

declare(strict_types=1);

if (!function_exists('formatBytes')) {
    function formatBytes(int $size, ?int $precision = 2): string
    {
        if ($size === 0) {
            return '0';
        }
        $base = log($size, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
}
