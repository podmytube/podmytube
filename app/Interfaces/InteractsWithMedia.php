<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Media;

interface InteractsWithMedia
{
    public function media(): Media;
}
