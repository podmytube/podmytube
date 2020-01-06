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
        $this->title = $channel->title ?? null;
        $this->description = $channel->description ?? null;
        $this->language = $channel->language ?? null;
        $this->copyright = $channel->copyright ?? null;
        $this->generator = $channel->generator ?? null;
        $this->webmaster = $channel->webmaster ?? null;
        $this->link = $channel->link ?? null;
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function render()
    {
        var_dump("rendering podcast into $destinationFile");
        return true;
    }

    public function addItunesHeader(ItunesHeader $itunesHeader)
    {
        $this->itunesHeader = $itunesHeader;
    }
}
