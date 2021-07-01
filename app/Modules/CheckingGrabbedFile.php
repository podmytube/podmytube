<?php

declare(strict_types=1);

namespace App\Modules;

use App\Exceptions\YoutubeAndLocalDurationException;

/**
 * This class goal is to get audio file information.
 */
class CheckingGrabbedFile
{
    protected const MINIMAL_SPREAD_BETWEEN_DURATION = 5;
    protected const ACCEPTABLE_SPREAD_RATIO = 0.004;

    /** @var \App\Modules\MediaProperties */
    protected $mediaProperties;

    protected $youtubeMediaDuration;

    private function __construct(MediaProperties $mediaProperties, ?int $youtubeMediaDuration = null)
    {
        $this->mediaProperties = $mediaProperties;
        $this->youtubeMediaDuration = $youtubeMediaDuration;
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function check()
    {
        $acceptableSpread = $this->minimalSpreadAccepted();

        /*
         * if difference between youtube duration and mp3 dration is more than 5 sec => exception
         * yt duration is one integer, duration returned by mp3 is seconds.microseconds.
         */
        if (abs($this->mediaProperties->duration() - $this->youtubeMediaDuration) > $acceptableSpread) {
            throw new YoutubeAndLocalDurationException(
                "Spread between Youtube duration {$this->youtubeMediaDuration} and \\
                audio file generated {$this->mediaProperties->duration()} is more than {{$acceptableSpread}} seconds !"
            );
        }

        return true;
    }

    /**
     * I'm accepting differences between youtube file api duration and local media duration.
     * Both duration are not measured the same way.
     * - For longest audio files I'm accepting a ratio between duration.
     * - for smallest audio files I keep 5 secs minimal spread
     * This function is doing this.
     */
    public function minimalSpreadAccepted()
    {
        $acceptableSpread = round($this->youtubeMediaDuration * self::ACCEPTABLE_SPREAD_RATIO);
        if ($acceptableSpread < self::MINIMAL_SPREAD_BETWEEN_DURATION) {
            return self::MINIMAL_SPREAD_BETWEEN_DURATION;
        }

        return $acceptableSpread;
    }
}
