<?php

declare(strict_types=1);

namespace App\Analytics;

use App\Analytics\Traits\IsCachable;
use App\Exceptions\LogLineIsEmptyException;
use App\Exceptions\LogProcessorMediaNotFoundException;
use App\Exceptions\LogProcessorUnknownChannelException;
use App\Exceptions\LogProcessorUnknownMediaException;
use App\Models\Channel;
use App\Models\Download;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class LogProcessor
{
    use IsCachable;

    public const RATIO_TO_BE_DOWNLOADED = 0.10; // file being downloaded at ~~80%~~ 10% is considered as downloaded

    protected string $cacheKeySeparator = '_';
    protected string $cachePrefix = 'Log';

    protected $fileHandle; // no typehint for resource at this day (2022-08-07 tyteck)
    protected string $logLine = '';
    protected int $nbProcessedLines = 0;
    protected int $nbValidLines = 0;
    protected int $nbMediasNotFound = 0;
    protected array $channelsAlreadyMet = [];
    protected array $errors = [];

    private function __construct(protected ?string $logsToParse = null)
    {
    }

    public static function with(...$params)
    {
        return new static(...$params);
    }

    public static function withFile(?string $logFileToProcess)
    {
        return new static(file_get_contents($logFileToProcess));
    }

    public function process(): self
    {
        $separator = PHP_EOL;
        $logLine = strtok($this->logsToParse, PHP_EOL);
        while ($logLine !== false) {
            try {
                $this->logLine = $logLine;
                $this->processLine();
                $this->nbValidLines++;
            } catch (LogProcessorMediaNotFoundException $exception) {
                // log line is valid but media file is not found
                $this->nbMediasNotFound++;
                $this->nbValidLines++;
            } catch (LogLineIsEmptyException $exception) {
                ray('empty log line')->green();
            } catch (Throwable $throwable) {
                ray($throwable->getMessage())->red();
            } finally {
                $this->nbProcessedLines++;
            }

            $logLine = strtok($separator);
        }
        // clean ram used by strtok
        strtok('', '');

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

    public function nbMediasNotFound()
    {
        return $this->nbMediasNotFound;
    }

    public function nbDownloadsByChannel(Channel $channel, Carbon $date): int
    {
        return Download::forChannelThisDay($channel, $date);
    }

    public function nbDownloadsByMedia(Media $media, Carbon $date): int
    {
        return Download::forMediaThisDay($media, $date);
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

        // 404 media not found
        throw_if($logLineParser->status() === 404, new LogProcessorMediaNotFoundException('Media {' . $logLineParser->mediaId() . '} is not found'));

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

        Download::query()->upsert(
            [
                'log_day' => $logLineParser->logDay(),
                'channel_id' => $channel->channelId(),
                'media_id' => $media->id,
                'counted' => 1,
            ],
            ['log_day', 'channel_id', 'media_id'],
            ['counted' => DB::raw('counted + 1')]
        );
    }

    protected function hasMediaBeenDownloaded(int $mediaSize, int $downloadedWeight)
    {
        return $downloadedWeight > $mediaSize * self::RATIO_TO_BE_DOWNLOADED;
    }
}
