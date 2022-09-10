<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToChannel;
use App\Traits\BelongsToPlan;
use App\Traits\IsRelatedToOneChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Channel $channel
 * @property Plan    $plan
 */
class Subscription extends Model
{
    use BelongsToChannel;
    use BelongsToPlan;
    use HasFactory;
    use IsRelatedToOneChannel;

    protected $fillable = ['channel_id', 'plan_id'];

    protected $casts = [
        'plan_id' => 'integer',
    ];
}
