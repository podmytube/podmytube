<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Exceptions\LogLineIsInvalidException;
use App\Exceptions\LogProcessorUnknownChannelException;
use App\Exceptions\LogProcessorUnknownMediaException;
use App\Models\Channel;
use App\Models\Media;
use Illuminate\Support\Facades\Cache;
use Throwable;

class LogProcessor
{
    public const RATIO_TO_BE_DOWNLOADED = 0.80; // file being downloaded at 80% is considered as downloaded
    public const CACHE_PREFIX = 'Log';
    public const SEPARATOR = '_';

    protected $fileHandle; // no typehint for resource at this day (2022-08-07 tyteck)
    protected int $nbProcessedLines = 0;
    protected int $nbValidLines = 0;
    protected array $channelsAlreadyMet = [];

    private function __construct(protected ?string $logFileToProcess = null)
    {
    }

    public static function with(...$params)
    {
        return new static(...$params);
    }

    public function process(): self
    {
        $this->startProcess();

        while (!feof($this->fileHandle)) {
            try {
                $logLine = fgets($this->fileHandle);
                throw_if($logLine === false, new LogLineIsInvalidException('logline is unreadable ' . $logLine));

                $this->processLine($logLine);
                $this->nbValidLines++;
            } catch (Throwable $throwable) {
                ray($throwable->getMessage())->red();
            } finally {
                $this->nbProcessedLines++;
            }
        }

        $this->endProcess();

        return $this;
    }

    public function nbProcessedLines()
    {
        return $this->nbProcessedLines;
    }

    public function nbValidLines()
    {
        return $this->nbValidLines;
    }

    public function isChannelMarkedUnknown(string $channelId): bool
    {
        return Cache::has(self::CACHE_PREFIX . self::SEPARATOR . 'UNKNOWN' . self::SEPARATOR . $channelId);
    }

    public function markChannelAsUnknownAndStore(string $channelId): void
    {
        Cache::put(self::CACHE_PREFIX . self::SEPARATOR . 'UNKNOWN' . self::SEPARATOR . $channelId, true);
    }

    public function hasChannelBeenMet(string $channelId)
    {
        return Cache::has(self::CACHE_PREFIX . self::SEPARATOR . 'CHANNEL' . self::SEPARATOR . $channelId);
    }

    public function markChannelAsMetAndStoreModel(Channel $channel): void
    {
        Cache::put(self::CACHE_PREFIX . self::SEPARATOR . 'CHANNEL' . self::SEPARATOR . $channel->channelId(), $channel);
    }

    public function recoverKnownChannelModel(string $channelId): Channel
    {
        return Cache::get(self::CACHE_PREFIX . self::SEPARATOR . 'CHANNEL' . self::SEPARATOR . $channelId);
    }

    public function recoverChannel(string $channelId): Channel
    {
        if ($this->hasChannelBeenMet($channelId)) {
            // we alreay met this channel => retrieve from cache
            $channel = $this->recoverKnownChannelModel($channelId);
        } else {
            // check channel exists
            $channel = Channel::byChannelId($channelId);
            if ($channel === null) {
                $this->markChannelAsUnknownAndStore($channelId);

                throw new LogProcessorUnknownChannelException('Channel ' . $channelId . ' is unknown');
            }
            // caching channel model
            $this->markChannelAsMetAndStoreModel($channel);
        }

        return $channel;
    }

    public function processLine(string $logLine): void
    {
        $logLineParser = LogLineParser::read($logLine)->parse();

        // we already met this channel and it is probably still unknown
        if ($this->isChannelMarkedUnknown($logLineParser->channelId())) {
            return; // skip
        }

        $channel = $this->recoverChannel($logLineParser->channelId());

        // check media exists
        $media = Media::byMediaId($logLineParser->mediaId());

        throw_if($media === false, new LogProcessorUnknownMediaException('Media ' . $logLineParser->mediaId() . ' is unknown'));

        // according to status media should be downloaded or not

        // check if media has been partially/totally downloaded
        $fully = $this->hasMediaBeenFullyDownloaded($media->weight(), $logLineParser->weight());

        /* the thing I want to store
            DAY --- CHANNEL_ID --- MEDIA_ID --- nb of downloads
        */
        Cache::increment(self::CACHE_PREFIX . self::SEPARATOR . $logLineParser->logDay() . self::SEPARATOR . $logLineParser->channelId() . self::SEPARATOR . $logLineParser->mediaId());
    }

    protected function startProcess(): void
    {
        $this->fileHandle = fopen($this->logFileToProcess, 'r');
    }

    protected function endProcess(): void
    {
        fclose($this->fileHandle);
    }

    protected function hasMediaBeenFullyDownloaded(int $mediaSize, int $downloadedWeight)
    {
        return $downloadedWeight > $mediaSize * self::RATIO_TO_BE_DOWNLOADED;
    }
}
