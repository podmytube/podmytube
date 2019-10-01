<?php

namespace App\Jobs;

use App\Thumb;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

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
            throw new ThumbDoesNotExistsException("Thumb {{$this->thumbToSend->id}} file does not exists. hard to send over sftp.");
        }
        Storage::disk('sftpthumbs')
            ->put(
                $this->thumbToSend->relativePath(),
                $this->thumbToSend->getData()
            );
    }
}
