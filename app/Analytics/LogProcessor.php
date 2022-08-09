<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Analytics\Traits\IsCachable;
use App\Exceptions\LogLineIsEmptyException;
use App\Exceptions\LogLineIsInvalidException;
use App\Exceptions\LogProcessorUnknownChannelException;
use App\Exceptions\LogProcessorUnknownMediaException;
use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class LogProcessor
{
    use IsCachable;

    public const RATIO_TO_BE_DOWNLOADED = 0.80; // file being downloaded at 80% is considered as downloaded

    protected string $cacheKeySeparator = '_';
    protected string $cachePrefix = 'Log';

    protected $fileHandle; // no typehint for resource at this day (2022-08-07 tyteck)
    protected string $logLine = '';
    protected int $nbProcessedLines = 0;
    protected int $nbValidLines = 0;
    protected array $channelsAlreadyMet = [];
    protected array $errors = [];

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

                throw_if($logLine === false, new LogLineIsInvalidException('logline is unreadable probably empty ' . $logLine));

                $this->logLine = $logLine;
                $this->processLine();
                $this->nbValidLines++;
            } catch (LogLineIsEmptyException $exception) {
                ray('empty log line')->green();
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

    public function nbDownloadsByChannel(Carbon $date, Channel $channel): int
    {
        return Download::where(
            ['log_day','=', $date->toDateString()]
            ['channel','=', $date->toDateString()]
            )->get()->count
    }

    protected function recoverChannelModel(string $channelId): ?Channel
    {
        if ($this->hasChannelBeenMet($channelId)) {
            // we alreay met this channel => retrieve from cache
            $channel = $this->recoverKnownChannelModel($channelId);
        } else {
            // check channel exists
            $channel = Channel::byChannelId($channelId);

            // caching channel model
            $this->markChannelAsMetAndStoreResult($channelId, $channel);
        }

        return $channel;
    }

    protected function recoverMediaModel(string $mediaId): ?Media
    {
        if ($this->hasMediaBeenMet($mediaId)) {
            // we alreay met this channel => retrieve from cache
            $media = $this->recoverKnownMediaModel($mediaId);
        } else {
            // check channel exists
            $media = Media::byMediaId($mediaId);

            // caching result
            $this->markMediaAsMetAndStoreResult($mediaId, $media);
        }

        return $media;
    }

    protected function processLine(): void
    {
        $logLineParser = LogLineParser::read($this->logLine)->parse();

        // recover channel model from cache or db
        $channel = $this->recoverChannelModel($logLineParser->channelId());
        throw_if($channel === null, new LogProcessorUnknownChannelException('Channel {' . $logLineParser->channelId() . '} is unknown'));

        // recover channel model from cache or db
        $media = $this->recoverMediaModel($logLineParser->mediaId());
        throw_if($media === null, new LogProcessorUnknownMediaException('Media {' . $logLineParser->mediaId() . '} is unknown'));

        // according to status media should be downloaded or not
        if ($logLineParser->status() !== 200 || $logLineParser->weight() <= 0) {
            ray('not a download only a touch');

            return;
        }

        // check if media has been partially/totally downloaded
        if (!$this->hasMediaBeenDownloaded($media->weight(), $logLineParser->weight())) {
            ray('bytes_sent is lower than 80%');

            return;
        }

        /* the thing I want to store
            DAY --- CHANNEL_ID --- MEDIA_ID --- nb of downloads
        */
        Download::query()->updateOrCreate(
            [
                'log_day' => $logLineParser->logDate(),
                'channel_id' => $logLineParser->channelId(),
                'media_id' => $logLineParser->mediaId(),
            ],
            ['count' => DB::raw('count + 1')]
        );
    }

    protected function startProcess(): void
    {
        $this->fileHandle = fopen($this->logFileToProcess, 'r');
    }

    protected function endProcess(): void
    {
        fclose($this->fileHandle);
    }

    protected function hasMediaBeenDownloaded(int $mediaSize, int $downloadedWeight)
    {
        return $downloadedWeight > $mediaSize * self::RATIO_TO_BE_DOWNLOADED;
    }
}
