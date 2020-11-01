<?php

namespace App\Modules;

/**
 * @category lib
 * @package  PodMyTube\core
 * @author   Frederick Tyteca <fred@podmytube.com>
 * @license  http://www.podmytube.com closed
 * @link     Podmytube website, http://www.podmytube.com
 */

use \wapmorgan\Mp3Info\Mp3Info;

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
            throw new \InvalidArgumentException("Media file {$mediaFile} does not exists", 1);
        }

        /**
         * Processing audio file with mediaObj
         * mediaObj fill an error if it gets into trouble with file.
         */
        try {
            $this->mediaObj = new Mp3Info($mediaFile, true);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Media file {$mediaFile} seem to be invalid", 1);
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
    public function getDuration()
    {
        if (!isset($this->mediaObj->duration)) {
            return 0;
        }
        return (int)$this->mediaObj->duration;
    }

    /**
     * This function will return filesize of mp3 file in bytes.
     *
     * @return string
     */
    public function getFilesize()
    {
        if (!isset($this->mediaObj->_fileSize)) {
            return 0;
        }
        return $this->mediaObj->_fileSize;
    }
};
