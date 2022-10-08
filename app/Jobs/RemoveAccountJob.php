<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveAccountJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var \App\Models\User */
    protected $userToRemove;

    public function __construct(User $userToRemove)
    {
        $this->userToRemove = $userToRemove;
    }

    public function handle(): void
    {
        $this->run();
    }

    public function run(): void
    {
        // remove all channels
        $this->userToRemove->channels->map(function ($channel): void {
            ChannelCleaningJob::dispatch($channel);
        });

        // delete podcastable (channel/playlist)
        $this->userToRemove->delete();
    }
}
