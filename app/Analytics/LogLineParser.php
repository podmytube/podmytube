<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Exceptions\LogLineInvalidDateException;
use App\Exceptions\LogLineIsEmptyException;
use App\Exceptions\LogLineIsInvalidException;
use Carbon\Carbon;

class LogLineParser
{
    public const DATE_FORMAT = 'd/M/Y:H:i:s O';
    public const QUERY_CHANNEL_ID_INDEX = 1;
    public const QUERY_MEDIA_ID_INDEX = 2;

    protected Carbon $logDate;
    protected string $method;
    protected string $query;
    protected int $status;
    protected int $weight;
    protected ?string $channelId = null;
    protected ?string $mediaId = null;

    private function __construct(protected ?string $logLine)
    {
    }

    public static function read(?string $logLine)
    {
        return new static($logLine);
    }

    public function parse(): self
    {
        if ($this->logLine === null || !strlen($this->logLine)) {
            throw new LogLineIsEmptyException('Line log parser is doing nothing with empty logs.');
        }

        // ============================================
        // Old log line (apache)
        // 172.18.0.5 - - [09/Aug/2021:15:54:09 +0200] "GET / HTTP/1.1" 200 2
        // $regexp = '#^(?P<ip>\S+) (\S+) (\S+) \[(?P<date>[^ ]+) [^\]]+\] \"(?P<method>GET|HEAD) (?P<query>[^ ]+) [^\"]+\" (?P<status>\d+) (?P<weight>\S+)$#';
        // ============================================
        // new log line (nginx) this is the FILE version
        // {"log":"172.18.0.4 - - [06/Aug/2022:18:32:40 +0200] \"GET / HTTP/1.1\" 200 2 \"-\" \"Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.79 Mobile Safari/537.36\" \"3.238.76.83, 172.70.175.193\"\n","stream":"stdout","time":"2022-08-06T16:32:40.208637699Z"}
        // $regexp = '#\{"log":"(?P<ip>\S+) (\S+) (\S+) \[(?P<date>[^\]]+)\] \\\"(?P<method>GET|HEAD) (?P<query>[^ ]+) (?P<HTTP>[^"]+)" (?P<status>\d+) (?P<weight>\d+) (.*)#';
        // ============================================
        // new log line (nginx) this is the docker logs command version
        // 172.18.0.3 - - [12/Aug/2022:22:30:19 +0200] "GET /UCu0tUATmSnMMCbCRRYXmVlQ/7XjfXJAJxWY.mp3 HTTP/1.1" 200 4260708 "https://podcasts-francais.fr/" "Mozilla/5.0 (Linux; Android 11; Pixel 2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Mobile Safari/537.36" "196.171.10.80, 172.70.86.19"
        $regexp = '#(?P<ip>\S+) (\S+) (\S+) \[(?P<date>[^\]]+)\] "(?P<method>GET|HEAD) (?P<query>[^ ]+) (?P<HTTP>[^"]+)" (?P<status>\d+) (?P<weight>\d+) (.*)#';
        if (!preg_match($regexp, $this->logLine, $matches)) {
            throw new LogLineIsInvalidException("This logline {{$this->logLine}} is invalid.");
        }

        if (!Carbon::canBeCreatedFromFormat($matches['date'], self::DATE_FORMAT)) {
            throw new LogLineInvalidDateException("This logline {{$this->logLine}} has one invalid date.");
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
        // 172.18.0.5 - - [09/Aug/2021:16:51:09 +0200] "HEAD /UCSMzy1n4Arqk_hCCOYOQn9g/B9BHzMWIYLI.mp3 HTTP/1.1" 200 -
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
        $successfulStatus = [200, 206, 304];

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

    public function weight(): int
    {
        return $this->weight ?? 0;
    }

    public function logDate(): Carbon
    {
        return $this->logDate;
    }

    public function logDay(): string
    {
        return $this->logDate->toDateString();
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
