<?php

namespace App\Podcast;

use App\Channel;
use App\Podcast\ItunesHeader;
use App\Podcast\PodcastCover;

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

        $this->podcastCover = null;
        $this->itunesHeader = null;
        /* $this->podcastCover = PodcastCover::prepare([
            "url" => $imageUrl,
            "link" => $imageLink,
            "title" => $imageTitle,
        ]);

        $this->itunesHeader = ItunesHeader::prepare([
            "author" => $authorName,
            "title" => $channel->title,
            "itunesOwner" => ItunesOwner::prepare($authorName, $authorEmail),
            "itunesCategory" => ItunesCategory::prepare(Category::find(86)),
            "explicit" => true,
        ]); */
    }

    public static function from(...$params)
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
