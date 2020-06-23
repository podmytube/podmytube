<?php

namespace App\Youtube;

/**
 * This class intends to get channels's playlist oredered by name.
 * 'uploads' => xliqsjfdumsldodsikpqs
 * 'favorites' => msldodsikpqsxliqsjfdu
 */
class YoutubeVideo extends YoutubeCore
{
    /** @var string $videoId */
    protected $videoId;

    public function __construct(string $videoId)
    {
        parent::__construct();
        $this->videoId = $videoId;
        $this->item = $this->defineEndpoint('/youtube/v3/videos')
            ->addParams(['id' => $this->videoId])
            ->addParts(['id', 'snippet', 'status'])
            ->run()
            ->items();
        return $this;
    }

    public function isAvailable()
    {
        return $this->item[0]['status']['uploadStatus'] === 'processed' &&
            $this->item[0]['snippet']['liveBroadcastContent'] === 'none';
    }
}
