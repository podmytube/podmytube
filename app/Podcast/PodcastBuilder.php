<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\SavingPodcastHasFailed;
use Illuminate\Support\Facades\Storage;

class PodcastBuilder
{
    public const _LOCAL_FEED_DISK = 'feeds';

    public const _REMOTE_FEED_DISK = 'sftpfeeds';

    public const _FEED_FILENAME = 'podcast.xml';

    /** @var Channel $channel is a Model/Channel object for the channel to generate */
    protected $channel;

    /** @var string $destinationFile where to save feed */
    protected $destinationFile;

    private function __construct(Channel $channel)
    {
        $this->channel = $channel;
        $this->podcastHeader = PodcastHeader::generateFor($channel);
        $this->podcastItems = PodcastItems::prepare($channel);
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function exists()
    {
        return Storage::disk(self::_LOCAL_FEED_DISK)->exists(
            $this->relativePath()
        );
    }

    /**
     * fetch templates and get feed data to be written.
     *
     * @return string podcast data
     */
    public function render()
    {
        return view('podcast.main')
            ->with(['podcast' => $this])
            ->render();
    }

    /**
     * This function will save podcast channel to its parking place (channelId/podcast.xml).
     *
     * @return true
     */
    public function save()
    {
        if (
            !Storage::disk(self::_LOCAL_FEED_DISK)->put(
                $this->relativePath(),
                $this->render()
            )
        ) {
            throw new SavingPodcastHasFailed(
                "An error occured while saving podcast to {{$this->destinationFile}}."
            );
        }
        return true;
    }

    public function path()
    {
        return Storage::disk(self::_LOCAL_FEED_DISK)->path(
            $this->relativePath()
        );
    }

    public function url()
    {
        return Storage::disk(self::_LOCAL_FEED_DISK)->url(
            $this->relativePath()
        );
    }

    /**
     * This function will give the relative path where to save podcast file.
     *
     * @return string relative path
     */
    public function relativePath()
    {
        return $this->channel->channelId() .
            DIRECTORY_SEPARATOR .
            self::_FEED_FILENAME;
    }

    /**
     * This function will return podcast header.
     * It is used in the podcast main template.
     *
     * @return string all the podcast header data.
     */
    public function podcastHeader()
    {
        return $this->podcastHeader;
    }

    /**
     * This function will return podcast items.
     * It is used in the podcast main template.
     *
     * @return string all the podcast items.
     */
    public function podcastItems()
    {
        return $this->podcastItems;
    }

    /**
     * This function will return the current channel built.
     */
    public function channel()
    {
        return $this->channel;
    }
}
