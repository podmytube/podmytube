<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Category;

trait HasOneCategory
{
    public function category()
    {
        return $this->hasOne(Category::class);
    }
}
