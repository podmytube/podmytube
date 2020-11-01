<?php

namespace App\Modules;

use InvalidArgumentException;
use Podmytube\Exceptions\AudioAndYoutubeDurationAreDifferent;

/**
 * This class goal is to get audio file information.
 */
class CheckAudioDuration
{
    protected const _MINIMAL_SPREAD_BETWEEN_DURATION = 5;

    /**
     * Check if duration are almost the same between YT and audio generated.
     *
     * @param YTVideoDetails video object details
     * @param string media id
     */
    public static function verify(YTVideoDetails $YTMediaProperties, string $mediaFile)
    {
        if (!file_exists($mediaFile)) {
            throw new InvalidArgumentException("Media file {$mediaFile} does not exists", 1);
        }

        /**
         * obtaining local video duration
         */
        $localAudioFileDuration = MediaProperties::analyzeFile($mediaFile)->getDuration();

        /**
         * obtaining yt media duration
         */
        $YTMediaDuration = $YTMediaProperties->duration();

        $acceptableSpread = ($acceptableSpread < self::_MINIMAL_SPREAD_BETWEEN_DURATION) ? self::_MINIMAL_SPREAD_BETWEEN_DURATION : $acceptableSpread;

        /**
         * if difference between youtube duration and mp3 dration is more than 5 sec => exception
         * yt duration is one integer, duration returned by mp3 is seconds.microseconds.
         * They cannot barely be equals so I'm checking if the difference between the two duration are >2 sec (1sec was not enough)
         */
        if (abs($localAudioFileDuration - $YTMediaDuration) > $acceptableSpread) {
            throw new AudioAndYoutubeDurationAreDifferent(
                "Spread between Youtube duration {{$YTMediaDuration}} and audio file generated {{$localAudioFileDuration}} is more than {{$acceptableSpread}} seconds !"
            );
        }
        return true;
    }
};
