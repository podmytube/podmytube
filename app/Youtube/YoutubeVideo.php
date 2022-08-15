<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Exceptions\YoutubeMediaDoesNotExistException;
use Carbon\Carbon;
use DateInterval;

class YoutubeVideo extends YoutubeCore
{
    protected const _PROCESSED_VIDEO_STATUS = 'processed';
    protected const _UPCOMING_VIDEO_STATUS = 'upcoming';

    /** @var string */
    protected $videoId;

    /**
     * @throws App\Exceptions\YoutubeMediaDoesNotExistException
     */
    private function __construct(string $videoId)
    {
        parent::__construct();
        $this->videoId = $videoId;

        $result = $this->defineEndpoint('/youtube/v3/videos')
            ->addParams(['id' => $this->videoId])
            ->addParts(['id', 'snippet', 'status', 'contentDetails'])
            ->run()
            ->items()
        ;

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
        return $this->item['status']['uploadStatus'] === 'processed'
            && $this->item['snippet']['liveBroadcastContent'] === 'none';
    }

    public function isTagged(): bool
    {
        return count($this->tags()) > 0;
    }

    public function tags(): array
    {
        return $this->item['snippet']['tags'] ?? [];
    }

    public function duration(): int
    {
        $interval = new DateInterval($this->item['contentDetails']['duration']);
        return ($interval->d * 24 * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
    }

    public function publishedAt(): string
    {
        return $this->item['snippet']['publishedAt'];
    }

    public function publishedAtForHumans(): string
    {
        return Carbon::parse($this->publishedAt())->diffForhumans();
    }

    public function title(): ?string
    {
        return $this->item['snippet']['title'];
    }

    public function videoId(): ?string
    {
        return $this->videoId;
    }

    public function description(): ?string
    {
        return $this->item['snippet']['description'];
    }

    public function item()
    {
        return $this->item;
    }
}
