<?php

namespace App\Listeners;

use App\Jobs\SendThumbBySFTP;
use App\Events\OccursOnChannel;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadThumb
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OccursOnChannel $event)
    {
        /**
         * This process will add the job SendThumbBySFTP to the queue (upload is long).
         * Jobs are runned with one supervisor.
         * !! IMPORTANT !! 
         * QUEUE_DRIVER in .env should be set to database and commands 
         * php artisan queue:table
         * php artisan migrate
         * should have been run
         */
        //SendThumbBySFTP::dispatch($event->thumb)->delay(now()->addMinutes(1));
        SendThumbBySFTP::dispatchNow($event->channel->thumb);
    }
}
