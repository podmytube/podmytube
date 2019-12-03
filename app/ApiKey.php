<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    public const _PROD_ENV = 1;
    public const _LOCAL_ENV = 2;
    
}
