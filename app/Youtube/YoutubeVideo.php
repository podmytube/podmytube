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

    private function __construct(string $videoId)
    {
        parent::__construct();
        $this->videoId = $videoId;
        $this->item = $this->defineEndpoint('/youtube/v3/videos')
            ->addParams(['id' => $this->videoId])
            ->addParts(['id', 'snippet', 'status'])
            ->run()
            ->items();
    }

    public static function forMedia(...$params)
    {
        return new static(...$params);
    }

    public function isAvailable()
    {
        return $this->item[0]['status']['uploadStatus'] === 'processed' &&
            $this->item[0]['snippet']['liveBroadcastContent'] === 'none';
    }

    public function tags()
    {
        return $this->item[0]['snippet']['tags'] ?? null;
    }
}
