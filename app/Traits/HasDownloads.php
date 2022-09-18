<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Download;

trait HasDownloads
{
    /**
     * define the relationship between one channel and its medias.
     */
    public function downloads()
    {
        return $this->HasMany(Download::class, 'channel_id');
    }
}
