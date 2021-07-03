<?php

declare(strict_types=1);

namespace App\Factories;

use App\Channel;
use App\Jobs\MediaCleaning;
use App\Media;

class DeleteChannelFactory
{
    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public static function media(Channel $channel)
    {
        return new static($channel);
    }

    public function run(): bool
    {
        $this->channel->medias->map(function ($media): void {
            MediaCleaning::dispatch($media);
        });

        return true;
    }
}
