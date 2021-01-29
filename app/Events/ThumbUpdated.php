<?php

namespace App\Events;

use App\Interfaces\Podcastable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ThumbUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Interfaces\Podcastable $podcastable */
    public $podcastable;

    public function __construct(Podcastable $podcastable)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . " - start for {$podcastable->podcastTitle()}");
        $this->podcastable = $podcastable;
    }
}
