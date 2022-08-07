<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Language;

trait HasOneLanguage
{
    public function language()
    {
        return $this->HasOne(Language::class, 'id', 'language_id');
    }
}
