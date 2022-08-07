<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\ThumbDoesNotExistsException;
use App\Exceptions\VignetteCreationFromThumbException;
use App\Models\Thumb;
use App\Modules\Vignette;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateVignetteFromThumb implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $srcThumb;

    /**
     * Create a new job instance.
     */
    public function __construct(Thumb $srcThumb)
    {
        $this->srcThumb = $srcThumb;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->srcThumb->exists()) {
            throw new ThumbDoesNotExistsException(
                "Thumb {{$this->srcThumb->fileName()}} file does not exists. hard to create vignette from it."
            );
        }

        try {
            // chaining vignette creation and upload
            Vignette::fromThumb($this->srcThumb)->makeIt()->saveLocally();
        } catch (Exception $exception) {
            $message = "Creation of vignette from thumb {{$this->srcThumb}} \\
                    for coverable {$this->srcThumb->coverableLabel()} has failed with message :" .
                    $exception->getMessage();
            Log::error($message);

            throw new VignetteCreationFromThumbException($message);
        }
    }
}
