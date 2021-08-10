<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\LineLogInvalidDateException;
use App\Exceptions\LineLogIsEmptyException;
use App\Exceptions\LineLogIsInvalidException;
use Carbon\Carbon;

class LineLogParserFactory
{
    public const DATE_FORMAT = 'd/M/Y:H:i:s';
    public const QUERY_CHANNEL_ID_INDEX = 1;
    public const QUERY_MEDIA_ID_INDEX = 2;

    /** @var string */
    protected $logLine;

    /** @var Carbon */
    protected $logDate;

    /** @var string */
    protected $method;

    /** @var string */
    protected $query;

    /** @var int */
    protected $status;

    /** @var int */
    protected $weight;

    /** @var string */
    protected $channelId;

    /** @var string */
    protected $mediaId;

    private function __construct(?string $logLine)
    {
        $this->logLine = $logLine;
    }

    public static function read(...$params)
    {
        return new static(...$params);
    }

    public function parse(): self
    {
        if ($this->logLine === null || !strlen($this->logLine)) {
            throw new LineLogIsEmptyException('Line log parser is doing nothing with empty logs.');
        }

        $regexp = '#^(?P<ip>\S+) (\S+) (\S+) \[(?P<date>[^ ]+) [^\]]+\] \"(?P<method>GET|HEAD) (?P<query>[^ ]+) [^\"]+\" (?P<status>\d+) (?P<weight>\S+)$#';
        if (!preg_match($regexp, $this->logLine, $matches)) {
            throw new LineLogIsInvalidException("This logline {{$this->logLine}} is invalid.");
        }

        if (!Carbon::canBeCreatedFromFormat($matches['date'], self::DATE_FORMAT)) {
            throw new LineLogInvalidDateException("This logline {{$this->logLine}} has one invalid date.");
        }

        $this->logDate = Carbon::createFromFormat(self::DATE_FORMAT, $matches['date']);
        $this->method = $matches['method'];
        $this->query = $matches['query'];
        $this->status = (int) $matches['status'];
        $this->weight = is_numeric($matches['weight']) ? intval($matches['weight']) : null;

        $this->extractPodmytubeData();

        return $this;
    }

    public function extractPodmytubeData(): bool
    {
        //172.18.0.5 - - [09/Aug/2021:16:51:09 +0200] "HEAD /UCSMzy1n4Arqk_hCCOYOQn9g/B9BHzMWIYLI.mp3 HTTP/1.1" 200 -
        $explodedQuery = explode(DIRECTORY_SEPARATOR, $this->query);
        if (count($explodedQuery) < 3) {
            return false;
        }

        $this->channelId = $explodedQuery[self::QUERY_CHANNEL_ID_INDEX];
        $this->mediaId = pathinfo($explodedQuery[self::QUERY_MEDIA_ID_INDEX], PATHINFO_FILENAME);

        return true;
    }

    public function isSuccessful(): bool
    {
        $successfulStatus = [200, 206];

        return in_array($this->status, $successfulStatus);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function weight(): ?int
    {
        return $this->weight;
    }

    public function logDate(): Carbon
    {
        return $this->logDate;
    }

    public function channelId(): ?string
    {
        return $this->channelId;
    }

    public function mediaId(): ?string
    {
        return $this->mediaId;
    }
}
