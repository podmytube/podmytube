<?php

namespace App\Modules;

use App\Exceptions\DownloadMediaFailureException;

/**
 * This class goal is to download youtube video and to convert it into one audio file
 */
class DownloadYTMedia
{
    public const AUDIO_FORMAT = 'mp3';

    /** the mediaId of the video */
    protected $mediaId;

    /** where to store the downloaded file */
    protected $destinationFolder;

    /** Will contain the path to the youtube-dl app */
    protected const YOUTUBE_DL_BINARY = '/usr/local/bin/youtube-dl';

    /** Parameters for youtube-dl */
    protected $youtubeDlparameters;

    /**
     * Full command line that has been used (debug purpose)
     */
    protected $commandLine;

    /**
     * Constructor, will check if youtube-dl is installed.
     *
     * @param string mediaToObtain the id of the video to download
     * @param string $audioFile where to store (locally) the audioFile
     */
    public function __construct(string $mediaToObtain, string $destinationFolder, bool $verbose = false)
    {
        /**
         * throw an exception if youtube-dl not installed
         */
        if (!file_exists(self::YOUTUBE_DL_BINARY)) {
            throw new \Exception('Youtube-dl is not installed on this server. You should install it first.');
        }

        $this->mediaId = $mediaToObtain;
        $this->verbose = $verbose;

        /**
         * Will set the path where to store the video file
         */
        $this->setDestinationFolder($destinationFolder);

        /**
         * Initialize default parameters
         */
        $this->setYoutubeDlParameters();

        /**
         * Initialize command line to be played
         */
        $this->setCommandLine();
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    /**
     * This function will set the destination folder (where to save it)
     *
     * @param string destinationFolder
     */
    protected function setDestinationFolder(string $destinationFolder)
    {
        if (!is_dir($destinationFolder) || !is_writable($destinationFolder)) {
            throw new \InvalidArgumentException("The folder {$destinationFolder} is either invalid or not writable");
        }
        $this->destinationFolder = $destinationFolder;
    }

    public function downloadedFilePath(): string
    {
        return $this->destinationFolder . $this->mediaId . '.' . self::AUDIO_FORMAT;
    }

    /**
     * This function will get the media id
     */
    protected function mediaId()
    {
        return $this->mediaId;
    }

    /**
     * This function will generate full command line to be used to grab video media
     */
    protected function setCommandLine()
    {
        $this->commandLine = self::YOUTUBE_DL_BINARY . ' ' . implode(' ', $this->getYoutubeDlParameters());
    }

    /**
     * This function will run youtube-dl command with wanted parameters in order to get the mp3 file.
     */
    public function download(): self
    {
        passthru($this->commandLine, $err);
        if ($err != 0) {
            throw new DownloadMediaFailureException('We failed to obtain media {' . $this->mediaId . "} with this command line : \n" . $this->commandLine);
        }
        return $this;
    }

    public function getYoutubeDlParameters()
    {
        return $this->youtubeDlparameters;
    }

    /**
     * This function will define the youtube-dl parameters to be used
     */
    protected function setYoutubeDlParameters()
    {
        $this->youtubeDlparameters = [
            '--no-warnings', // Ignore warnings
            '--extract-audio', // Convert video files to audio-only files (requires ffmpeg)
            '--audio-format ' . self::AUDIO_FORMAT, // post processing option to convert file obtained to mp3
            "--format 'bestaudio[ext=mp3]/best[ext=webm]/best'", // Download best (else dl is slow)
            "--output '" . $this->destinationFolder . "%(id)s.%(ext)s'",
        ];

        if (!$this->verbose) {
            $this->youtubeDlparameters[] = '--quiet';
        }

        $this->youtubeDlparameters[] = 'https://www.youtube.com/watch?v=' . $this->mediaId();

        if (!$this->verbose) {
            $this->youtubeDlparameters[] = '>/dev/null 2>&1';
        }
    }
}
