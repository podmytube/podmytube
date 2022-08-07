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

        /* WARNING
         * You MUST keep dispatchSync
         * the problem : Im generating a temporary file with the podcast content.
         * if I dispatch the job, with the delay, temporary file may be reused by another podcast.
         * if I dispatchSync it, file is transfered immediately so no usurpation.
         */
        SendFileBySFTP::dispatchSync($this->localPath, $this->remotePath(), $cleanAfter = true);

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
        $localPath .= now()->format('Y-m-d\TH:i') . '_';
        $localPath .= $this->podcastable instanceof Channel ? 'channel' : 'playlist';
        $localPath .= '_' . $this->podcastable->channelId();

        return $localPath;
    }

    protected function saveRenderedFile(string $renderedPodcast): string
    {
        $localPath = $this->prepareLocalPath();

        // saving podcast locally
        $status = file_put_contents($localPath, $renderedPodcast);
        if ($status === false) {
            throw new PodcastSavingFailureException("Saving rendered podcast to {$localPath} has failed.");
        }

        return $localPath;
    }
}
