<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;
}
