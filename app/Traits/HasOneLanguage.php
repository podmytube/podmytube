<?php

namespace App\Traits;

use App\Language;

trait HasOneLanguage
{
    public function language()
    {
        return $this->HasOne(Language::class, 'id', 'language_id');
    }
}
