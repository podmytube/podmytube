<?php

declare(strict_types=1);

namespace Database\Factories\Traits;

use App\Models\Channel;

trait HasChannel
{
    public function channel(Channel $channel): static
    {
        return $this->state(['channel_id' => $channel->id()]);
    }
}
