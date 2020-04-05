<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\FeedDoesNotExist;
use App\Exceptions\SavingPodcastHasFailed;

use Illuminate\Support\Facades\Storage;

class PodcastUpload
{
    public const _LOCAL_FEED_DISK = 'feeds';

    public const _REMOTE_FEED_DISK = 'sftpfeeds';

    public const _FEED_FILENAME = 'podcast.xml';

    /** @var Channel $channel is a Model/Channel object for the channel to generate */
    protected $channel;

    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
        if (!$this->feedExists()) {
            throw new FeedDoesNotExist('Feed for channel does not exist.');
        }
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function feedExists()
    {
        return Storage::disk(self::_LOCAL_FEED_DISK)->exists(
            $this->relativePath()
        );
    }

    /**
     * return the relative file path
     *
     * @return string relative path
     */
    public function relativePath()
    {
        return $this->channel->channelId() .
            DIRECTORY_SEPARATOR .
            self::_FEED_FILENAME;
    }

    public function upload()
    {
        /** uploading channelId/podcast.xml */
        Storage::disk(self::_REMOTE_FEED_DISK)->put(
            $this->relativePath(),
            Storage::disk(self::_LOCAL_FEED_DISK)->get($this->relativePath())
        );

        /** granting +x perms to channelId/ */
        Storage::disk(self::_REMOTE_FEED_DISK)->setVisibility(
            $this->channel->channelId(),
            'public'
        );
        return true;
    }
}
