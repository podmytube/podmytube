<?php

namespace App\Jobs;

use App\Mail\ChannelHasReachedItsLimits;
use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class MailChannelHasReachedItsLimit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $media;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('sending mail ChannelHasReachedItsLimits');
        Mail::to($this->media->media->user)->send(
            new ChannelHasReachedItsLimits($this->media)
        );
    }
}
