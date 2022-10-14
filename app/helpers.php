<?php

declare(strict_types=1);

use Carbon\CarbonInterval;
use Spatie\Ssh\Ssh;

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

if (!function_exists('secondsToYoutubeFormat')) {
    function secondsToYoutubeFormat(int $seconds): string
    {
        return CarbonInterval::seconds($seconds)->cascade()->spec();
    }
}

if (!function_exists('sshPod')) {
    function sshPod(): Ssh
    {
        return Ssh::create(config('app.podhost_ssh_user'), config('app.podhost_ssh_host'))
            ->disableStrictHostKeyChecking()
            ->usePrivateKey(config('app.sftp_key_path'))
        ;
    }
}

if (!function_exists('youtubeChannelUrl')) {
    function youtubeChannelUrl(string $channelId): string
    {
        return 'https://www.youtube.com/channel/' . $channelId;
    }
}

if (!function_exists('fixtures_path')) {
    function fixtures_path(string $relativePath): string
    {
        return base_path('tests/Fixtures/' . ltrim($relativePath, '/'));
    }
}

if (!function_exists('encodeLikeLaravel')) {
    function encodeLikeLaravel(string $toBeEncoded): string
    {
        return htmlspecialchars($toBeEncoded, ENT_QUOTES | ENT_HTML401);
    }
}

if (!function_exists('defaultVignetteUrl')) {
    function defaultVignetteUrl(): string
    {
        return config('app.thumbs_url') . '/default_vignette.jpg';
    }
}

if (!function_exists('defaultCoverUrl')) {
    function defaultCoverUrl(): string
    {
        return config('app.thumbs_url') . '/default_thumb.jpg';
    }
}
