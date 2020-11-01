<?php

namespace App\Youtube;

use App\Exceptions\YoutubeMediaDoesNotExistException;

class YoutubeVideo extends YoutubeCore
{
    /** @var string $videoId */
    protected $videoId;

    private function __construct(string $videoId)
    {
        parent::__construct();
        $this->videoId = $videoId;

        $result = $this->defineEndpoint('/youtube/v3/videos')
            ->addParams(['id' => $this->videoId])
            ->addParts(['id', 'snippet', 'status'])
            ->run()
            ->items();

        if (!count($result)) {
            throw new YoutubeMediaDoesNotExistException("This media {$this->videoId} does not exist on youtube.");
        }

        $this->item = $result[0];
    }

    public static function forMedia(...$params)
    {
        return new static(...$params);
    }

    public function isAvailable(): bool
    {
        return $this->item['status']['uploadStatus'] === 'processed' &&
            $this->item['snippet']['liveBroadcastContent'] === 'none';
    }

    public function isTagged(): bool
    {
        return count($this->tags());
    }

    public function tags(): ?array
    {
        return $this->item['snippet']['tags'] ?? [];
    }
}
