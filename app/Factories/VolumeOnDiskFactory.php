<?php

declare(strict_types=1);

namespace App\Factories;

use App\Media;

class VolumeOnDiskFactory
{
    private function __construct()
    {
        //code
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function raw(): int
    {
        // get all active subscriptions
        return Media::query()
            ->grabbedAt()
            ->select('length')
            ->get()
            ->reduce(function ($carry, Media $media): int {
                // on each media get plan price
                return $carry + $media->length;
            }, 0)
        ;
    }

    public function formatted(int $precision = 2): string
    {
        return formatBytes($this->raw(), $precision);
    }
}
