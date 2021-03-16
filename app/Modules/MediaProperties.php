<?php

namespace App\Modules;

/**
 * @category lib
 *
 * @package  PodMyTube\core
 *
 * @author   Frederick Tyteca <fred@podmytube.com>
 *
 * @license  http://www.podmytube.com closed
 *
 * @link     Podmytube website, http://www.podmytube.com
 */

use getID3;
use InvalidArgumentException;

/**
 * This class goal is to get audio file information.
 */
class MediaProperties
{
    /**
     * mediaObj object
     */
    protected $mediaObj;

    /**
     * audio file informations as returned by mediaObj library
     */
    protected $mediaFileInformations;

    /**
     * Constructor, will receive one file (absolute path)
     */
    public function __construct(string $mediaFile)
    {
        if (!file_exists($mediaFile)) {
            throw new InvalidArgumentException("Media file {$mediaFile} does not exists", 1);
        }

        /**
         * Processing audio file with mediaObj
         * mediaObj fill an error if it gets into trouble with file.
         */
        $this->mediaObj = new getID3();
        $this->mediaObj->analyze($mediaFile);
        if (isset($this->mediaObj->info['error'])) {
            $errors = implode("\n", $this->mediaObj->info['error']);
            throw new InvalidArgumentException("Error analyzing media file {$mediaFile}. {$errors}", 1);
        }
    }

    public static function analyzeFile(...$params)
    {
        return new static(...$params);
    }

    /**
     * This function will return duration of mp3 file in seconds.
     *
     * @return string
     */
    public function duration()
    {
        return (int) round($this->mediaObj->info['playtime_seconds']);
    }

    /**
     * This function will return filesize of mp3 file in bytes.
     *
     * @return string
     */
    public function filesize()
    {
        return (int) $this->mediaObj->info['filesize'];
    }
}
