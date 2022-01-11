<?php

declare(strict_types=1);

namespace App\Factories;

use App\Channel;
use App\Exceptions\PodcastSavingFailureException;
use App\Interfaces\Podcastable;
use App\Jobs\SendFileBySFTP;
use App\Podcast\PodcastBuilder;
use Illuminate\Support\Facades\Log;

class UploadPodcastFactory
{
    protected Podcastable $podcastable;
    protected string $localPath;

    private function __construct()
    {
    }

    public static function init()
    {
        return new static();
    }

    public function for(Podcastable $podcastable)
    {
        $this->podcastable = $podcastable;

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

        Log::notice("Podcast {$podcastable->podcastTitle()} has been successfully updated. You can check it here : {$podcastable->podcastUrl()}");

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

    protected function saveRenderedFile(string $renderedPodcast): string
    {
        $localPath = '/tmp/';
        $localPath .= now()->format('Y-m-d\TH:i:s') . '_';
        $localPath .= $this->podcastable instanceof Channel ? 'channel' : 'playlist';

        // saving podcast locally
        $status = file_put_contents($localPath, $renderedPodcast);
        if ($status === false) {
            throw new PodcastSavingFailureException("Saving rendered podcast to {$localPath} has failed.");
        }

        return $localPath;
    }
}
