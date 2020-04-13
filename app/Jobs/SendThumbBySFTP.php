<?php

namespace App\Jobs;

use App\Exceptions\ThumbDoesNotExistsException;
use App\Exceptions\ThumbUploadHasFailedException;
use App\Thumb;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendThumbBySFTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thumbToSend;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Thumb $thumb)
    {
        $this->thumbToSend = $thumb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->thumbToSend->exists()) {
            throw new ThumbDoesNotExistsException(
                "Thumb {{$this->thumbToSend->id}} file does not exists. hard to send over sftp."
            );
        }

        try {
            $this->thumbToSend->upload();
        } catch (\Exception $exception) {
            throw new ThumbUploadHasFailedException(
                "The upload of thumb {{$this->thumbToSend}} for channel {{$this->thumbToSend->channel_id}} has failed with message :" .
                    $e->getMessage()
            );
        }
    }
}
