<?php

namespace App\Jobs;

use App\Exceptions\ThumbDoesNotExistsException;
use App\Exceptions\VignetteCreationFromThumbException;
use App\Modules\Vignette;
use App\Thumb;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
            throw new ThumbDoesNotExistsException(
                "Thumb {{$this->srcThumb->fileName()}} file does not exists. hard to create vignette from it."
            );
        }

        try {
            /** chaining vignette creation and upload */
            $vignette = Vignette::fromThumb($this->srcThumb)->makeIt()->saveLocally();
        } catch (\Exception $exception) {
            $message = "Creation of vignette from thumb {{$this->srcThumb}} for channel {{$this->srcThumb->channel_id}} has failed with message :" .
                    $exception->getMessage();
            throw new VignetteCreationFromThumbException($message);
            Log::debug($message);
        }
    }
}
