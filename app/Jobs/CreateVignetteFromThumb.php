<?php

namespace App\Jobs;

use App\Exceptions\ThumbDoesNotExistsException;
use App\Exceptions\VignetteCreationFromThumbException;
use App\Modules\Vignette;
use App\Thumb;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateVignetteFromThumb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $srcThumb;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Thumb $srcThumb)
    {
        $this->srcThumb = $srcThumb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->srcThumb->exists()) {
            throw new ThumbDoesNotExistsException("Thumb {{$this->srcThumb->fileName()}} file does not exists. hard to create vignette from it.");
        }

        try {
            /** chaining vignette creation and upload */
            Vignette::fromThumb($this->srcThumb)->makeIt()->upload();
        } catch (\Exception $e) {
            throw new VignetteCreationFromThumbException(
                "Creation of vignette from thumb {{$this->srcThumb}} for channel {{$this->srcThumb->channel_id}} has failed with message :" .
                    $e->getMessage()
            );
        }
    }
}
