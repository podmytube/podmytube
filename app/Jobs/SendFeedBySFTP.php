<?php

namespace App\Jobs;

use App\Channel;
use App\Events\OccursOnChannel;
use App\Podcast\PodcastUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFeedBySFTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $channel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OccursOnChannel $event)
    {
        Log::notice(__CLASS__ . '::' . __FUNCTION__);
        Log::notice("{$event->channel->channel_name} is about to be uploaded.");
        PodcastUpload::prepare($this->channel)->upload();
        Log::notice("{$event->channel->channel_name} has been uploaded.");
    }
}
