<?php

namespace App\Podcast;

use App\Channel;
use App\Podcast\ItunesHeader;
use App\Podcast\PodcastCover;
use App\Thumb;

class PodcastHeader
{
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

    private function __construct(Channel $channel)
    {
        $this->link = $channel->link ?? null;
        $this->title = $channel->title();
        $this->language = $channel->lang ?? null;
        $this->copyright = $channel->podcast_copyright ?? null;
        $this->description = $channel->description ?? null;

        $this->itunesHeader = null;

        $this->podcastCover = PodcastCover::prepare([
            "url" => isset($channel->thumb) ? $channel->thumb->podcastUrl() : Thumb::defaultUrl(),
            "link" => $channel->link,
            "title" => $channel->title(),
        ]);

        $this->itunesHeader = ItunesHeader::prepare([
            "author" => $channel->authors,
            "title" => $channel->title(),
            "imageUrl" => isset($channel->thumb) ? $channel->thumb->podcastUrl() : Thumb::defaultUrl(),
            "itunesOwner" => ItunesOwner::prepare($channel->authors, $channel->email),
            "itunesCategory" => ItunesCategory::prepare($channel->category),
            "explicit" => $channel->explicit,
        ]);
    }

    public static function generateFor(...$params)
    {
        return new static(...$params);
    }

    public function render()
    {
        $dataToRender = array_filter(get_object_vars($this), function ($property) {
            if (isset($property)) {
                return true;
            }
            return false;
        });
        if (!$dataToRender) {
            return "";
        }
        return view('podcast.header')->with(["podcastHeader" => $this])->render();
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
