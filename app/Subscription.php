<?php

declare(strict_types=1);

namespace App;

use App\Traits\BelongsToChannel;
use App\Traits\BelongsToPlan;
use App\Traits\IsRelatedToOneChannel;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToChannel;
    use BelongsToPlan;
    use IsRelatedToOneChannel;

    protected $fillable = ['channel_id', 'plan_id'];

    protected $casts = [
        'plan_id' => 'integer',
    ];
}
