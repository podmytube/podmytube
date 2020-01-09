<?php

namespace App\Podcast;

use App\Channel;
use Carbon\Carbon;

class PodcastHeader
{
    protected $items = [];
    protected $docs;
    protected $link;
    protected $title;
    protected $description;
    protected $language;
    protected $copyright;
    protected $generator;
    protected $pubDate;
    protected $lastBuildDate;
    protected $webmaster;

    private function __construct(Channel $channel)
    {
        $this->link = $channel->link ?? null;
        $this->title = $channel->title ?? null;
        $this->language = $channel->language ?? null;
        $this->copyright = $channel->copyright ?? null;
        $this->description = $channel->description ?? null;
    }

    public static function prepare(...$params)
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
        return view('podcast.header')->with(["header" => $this])->render();
    }

    public function addItunesHeader(ItunesHeader $itunesHeader)
    {
        $this->itunesHeader = $itunesHeader;
    }
}
