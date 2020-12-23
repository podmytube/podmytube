<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\FeedDoesNotExistException;
use App\Exceptions\PodcastUpdateFailureException;
use Illuminate\Support\Facades\Storage;

class PodcastUpload
{
    public const LOCAL_FEED_DISK = 'feeds';
    public const REMOTE_FEED_DISK = 'sftpfeeds';
    public const FEED_FILENAME = 'podcast.xml';

    /** @var Channel $channel is a Model/Channel object for the channel to generate */
    protected $channel;

    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
        if (!$this->feedExists()) {
            throw new FeedDoesNotExistException('Feed for channel does not exist.');
        }
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function feedExists()
    {
        return Storage::disk(self::LOCAL_FEED_DISK)->exists($this->relativePath());
    }

    /**
     * return the relative file path
     *
     * @return string relative path
     */
    public function relativePath()
    {
        return $this->channel->channelId() . '/' . self::FEED_FILENAME;
    }

    public function upload()
    {
        /** uploading channelId/podcast.xml */
        $feedUploadResult = Storage::disk(self::REMOTE_FEED_DISK)->put(
            $this->relativePath(),
            Storage::disk(self::LOCAL_FEED_DISK)->get($this->relativePath())
        );
        if ($feedUploadResult === false) {
            throw new PodcastUpdateFailureException('Uploading feed on remote host has failed.');
        }

        /** granting +x perms to channelId/ */
        return Storage::disk(self::REMOTE_FEED_DISK)->setVisibility(
            $this->channel->channelId(),
            'public'
        );

        return true;
    }

    public function remoteFeedExists(): bool
    {
        return Storage::disk(self::REMOTE_FEED_DISK)->exists($this->relativePath());
    }
}
