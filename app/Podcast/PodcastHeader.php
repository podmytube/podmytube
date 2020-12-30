<?php

namespace App\Podcast;

use App\Channel;
use App\Playlist;
use App\Thumb;

class PodcastHeader
{
    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\Playlist $playlist */
    protected $playlist;

    /** @var string url of the show */
    protected $link;

    /** @var string title of the show (channel name or podcast title) */
    protected $title;

    /** @var string The show language. */
    protected $language;

    /** @var string The show copyright details. */
    protected $copyright;

    /** @var string the show description */
    protected $description;

    /** @var PodcastCover rss tags for podcast thumb */
    protected $podcastCover;

    /** @var ItunesHeader itunes properties to be set in header */
    protected $itunesHeader;

    private function __construct()
    {
    }

    public function forChannel(Channel $channel)
    {
        $this->channel = $channel;
        return $this->prepare();
    }

    public function forPlaylist(Playlist $playlist)
    {
        $this->channel = $playlist->channel;
        $this->playlist = $playlist->channel;
        return $this->prepare();
    }

    public function prepare()
    {
        $this->link = $this->channel->link ?? null;
        $this->title = $this->playlist ? $this->playlist->title() : $this->channel->title();
        $this->language = $this->channel->language->code ?? null;
        $this->copyright = $this->channel->podcast_copyright ?? null;
        $this->description = $this->channel->description ?? null;

        $this->itunesHeader = null;

        $this->podcastCover = PodcastCover::prepare([
            'url' => isset($this->channel->thumb)
                ? $this->channel->thumb->podcastUrl()
                : Thumb::defaultUrl(),
            'link' => $this->channel->link,
            'title' => $this->channel->title(),
        ]);

        $this->itunesHeader = ItunesHeader::prepare([
            'author' => $this->channel->authors,
            'title' => $this->channel->title(),
            'imageUrl' => isset($this->channel->thumb)
                ? $this->channel->thumb->podcastUrl()
                : Thumb::defaultUrl(),
            'itunesOwner' => ItunesOwner::prepare(
                $this->channel->authors,
                $this->channel->email
            ),
            'itunesCategory' => ItunesCategory::prepare($this->channel->category),
            'explicit' => $this->channel->explicit,
        ]);
        return $this;
    }

    public static function init()
    {
        return new static();
    }

    public function render()
    {
        $dataToRender = array_filter(get_object_vars($this), function (
            $property
        ) {
            if (isset($property)) {
                return true;
            }
            return false;
        });
        if (!$dataToRender) {
            return '';
        }
        return view('podcast.header')
            ->with(['podcastHeader' => $this])
            ->render();
    }

    public function link()
    {
        return $this->link;
    }

    public function title()
    {
        return $this->title;
    }

    public function language()
    {
        return $this->language;
    }

    public function copyright()
    {
        return $this->copyright;
    }

    public function description()
    {
        return $this->description;
    }

    public function podcastCover()
    {
        return $this->podcastCover;
    }

    public function itunesHeader()
    {
        return $this->itunesHeader;
    }
}
