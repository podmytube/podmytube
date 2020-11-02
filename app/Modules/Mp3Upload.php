<?php

namespace App\Podcast;

use Illuminate\Support\Facades\Storage;

class Mp3Upload
{
    public const LOCAL_FEED_DISK = 'feeds';
    public const REMOTE_FEED_DISK = 'sftpfeeds';
    public const FEED_FILENAME = 'podcast.xml';

    /** @var Channel $channel is a Model/Channel object for the channel to generate */
    protected $channel;

    private function __construct($audioFileToUpload)
    {

    }

    public static function prepare(...$params)
    {
        return new static(...$params);
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
            self::FEED_FILENAME;
    }

    public function upload()
    {
        /** uploading channelId/podcast.xml */
        Storage::disk(self::REMOTE_FEED_DISK)->put(
            $this->relativePath(),
            Storage::disk(self::LOCAL_FEED_DISK)->get($this->relativePath())
        );

        /** granting +x perms to channelId/ */
        Storage::disk(self::REMOTE_FEED_DISK)->setVisibility(
            $this->channel->channelId(),
            'public'
        );
        return true;
    }
}
