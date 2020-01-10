<?php

namespace App\Podcast;

use App\Channel;

class PodcastBuilder
{
    /** @var Channel $channel is a Model/Channel object for the channel to generate */
    protected $channel;

    /** @var string $destinationFile where to save feed */
    protected $destinationFile;

    private function __construct(Channel $channel, string $destinationFile)
    {
        $this->channel = $channel;
        $this->setDestinationFile($destinationFile);
        $this->podcastHeader = PodcastHeader::generateFor($channel);
        $this->podcastItems = PodcastItems::prepare($channel);
    }

    public static function prepare(...$params)
    {
        return new static(...$params);
    }

    public function setDestinationFile(string $destinationFile)
    {
        if (!is_writable(pathinfo($destinationFile, PATHINFO_DIRNAME))) {
            throw new \InvalidArgumentException("Destination folder for {{$destinationFile}} is not writable.");
        }
        $this->destinationFile = $destinationFile;
    }

    public function render()
    {
        return view('podcast.main')->with(["podcast" => $this])->render();
    }

    public function podcastHeader()
    {
        return $this->podcastHeader;
    }

    public function podcastItems()
    {
        return $this->podcastItems;
    }
}
