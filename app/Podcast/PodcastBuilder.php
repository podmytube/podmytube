<?php

namespace App\Podcast;

use App\Channel;
use App\Exceptions\SavingPodcastHasFailed;
use App\Playlist;
use Illuminate\Support\Facades\Storage;

class PodcastBuilder
{
    public const LOCAL_FEED_DISK = 'feeds';

    public const FEED_FILENAME = 'podcast.xml';

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Playlist $playlist */
    protected $playlist;

    /** @var string $destinationFile where to save feed */
    protected $destinationFile;

    private function __construct()
    {
    }

    public static function init()
    {
        return new static();
    }

    public function forChannel(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function forPlaylist(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    public function build()
    {
        $this->podcastHeader = PodcastHeader::generateFor($this->channel);
        $this->podcastItems = PodcastItems::prepare($this->channel);
        return $this;
    }

    public function exists()
    {
        return Storage::disk(self::LOCAL_FEED_DISK)->exists(
            $this->feedRelativePath()
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
    public function save():bool
    {
        if (
            !Storage::disk(self::LOCAL_FEED_DISK)->put(
                $this->feedRelativePath(),
                $this->render()
            )
        ) {
            throw new SavingPodcastHasFailed(
                "An error occured while saving podcast to {{$this->destinationFile}}."
            );
        }
        return true;
    }

    /**
     * Should return the absolute path of the feed.
     *
     * @return string absolute path of the podcast file
     */
    public function path():string
    {
        return Storage::disk(self::LOCAL_FEED_DISK)->path(
            $this->feedRelativePath()
        );
    }

    public function url()
    {
        return Storage::disk(self::LOCAL_FEED_DISK)->url($this->feedRelativePath());
    }

    /**
     * Return relative path for saves podcast file.
     * Should return something like channel_id/podcast.xml
     *
     * @return string relative path
     */
    public function feedRelativePath()
    {
        return $this->channel->channelId() . '/' . self::FEED_FILENAME;
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
    public function channel() :\App\Channel
    {
        return $this->channel;
    }
}
