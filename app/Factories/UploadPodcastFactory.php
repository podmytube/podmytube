<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\PodcastSavingFailureException;
use App\Interfaces\Podcastable;
use App\Jobs\SendFileBySFTP;
use App\Models\Channel;
use App\Podcast\PodcastBuilder;
use Illuminate\Support\Facades\Log;

class UploadPodcastFactory
{
    protected string $localPath;

    private function __construct(protected Podcastable $podcastable)
    {
    }

    public static function for(Podcastable $podcastable)
    {
        return new static($podcastable);
    }

    public function run()
    {
        /** getting rendered podcast */
        $renderedPodcast = PodcastBuilder::create($this->podcastable->toPodcast())->render();

        // defining where to render local path
        $this->localPath = $this->saveRenderedFile($renderedPodcast);

        SendFileBySFTP::dispatch($this->localPath, $this->remotePath(), $cleanAfter = true)
            ->delay(now()->addSeconds(3))
        ;

        $this->podcastable->wasUpdatedOn(now());

        Log::notice("Podcast {$this->podcastable->podcastTitle()} has been successfully updated. You can check it here : {$this->podcastable->podcastUrl()}");

        return $this;
    }

    public function localPath(): string
    {
        return $this->localPath;
    }

    public function remotePath(): string
    {
        return $this->podcastable->remoteFilePath();
    }

    public function prepareLocalPath(): string
    {
        $localPath = '/tmp/';
        $localPath .= now()->format('Y-m-d\THis') . '_';
        $localPath .= $this->podcastable instanceof Channel ? 'channel' : 'playlist';
        $localPath .= '_' . $this->podcastable->channelId();

        return $localPath;
    }

    protected function saveRenderedFile(string $renderedPodcast): string
    {
        $localPath = $this->prepareLocalPath();

        // saving podcast locally
        $status = file_put_contents($localPath, $renderedPodcast);
        throw_if($status === false, new PodcastSavingFailureException("Saving rendered podcast to {$localPath} has failed."));

        return $localPath;
    }
}
