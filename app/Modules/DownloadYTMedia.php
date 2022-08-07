<?php

declare(strict_types=1);

namespace App\Modules;

use App\Exceptions\DownloadMediaFailureException;
use App\Models\Media;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * This class goal is to download youtube video and to convert it into one audio file.
 */
class DownloadYTMedia
{
    public const AUDIO_FORMAT = 'mp3';
    public const VIDEO_FORMAT = 'mp4';

    /** Will contain the path to the youtube-dl app */
    protected const YOUTUBE_DL_BINARY = '/usr/local/bin/yt-dlp';

    /** @var \App\Models\Media */
    protected $media;

    /** @var bool */
    protected $verbose = false;

    /** @var string (IE : /tmp/ ) */
    protected $destinationFolder;

    /** Parameters for youtube-dl */
    protected $youtubeDlparameters;

    /**
     * Full command line that has been used (debug purpose).
     */
    protected $commandLine;

    /**
     * Constructor, will check if youtube-dl is installed.
     *
     * @param \App\Models\Media $mediaToObtain     the id of the video to download
     * @param string            $destinationFolder where to store (locally) the audioFile
     */
    public function __construct(Media $mediaToObtain, string $destinationFolder, bool $verbose = false)
    {
        $this->media = $mediaToObtain;
        $this->destinationFolder = $destinationFolder;
        $this->verbose = $verbose;

        // Will set the path where to store the video file
        $this->checkDestinationFolder();

        // Clean the way before downloading video + audio
        $this->cleanPreviouslyDownloaded();

        // Initialize default parameters
        $this->youtubeDlParameters();

        // Initialize command line to be played
        $this->buildCommandLine();
    }

    public static function init(Media $mediaToObtain, string $destinationFolder, bool $verbose = false)
    {
        return new static($mediaToObtain, $destinationFolder, $verbose);
    }

    public function downloadedFilePath(): string
    {
        return $this->destinationFolder . $this->media->media_id . '.' . self::AUDIO_FORMAT;
    }

    public function downloadedVideoFilePath()
    {
        return $this->destinationFolder . $this->media->media_id . '.' . self::VIDEO_FORMAT;
    }

    /**
     * This function will run youtube-dl command with wanted parameters in order to get the mp3 file.
     */
    public function download(): self
    {
        passthru($this->commandLine, $err);
        if ($err !== 0) {
            $message = "Downloading media '{$this->media->media_id}' for channel {$this->media->channel->nameWithId()} has failed.";
            Log::error($message, ['err' => $err, 'cmd' => $this->commandLine]);

            throw new DownloadMediaFailureException($message);
        }

        return $this;
    }

    public function getYoutubeDlParameters()
    {
        return $this->youtubeDlparameters;
    }

    public function commandLine(): string
    {
        return $this->commandLine;
    }

    /**
     * This function will set the destination folder (where to save it).
     *
     * @param string destinationFolder
     */
    protected function checkDestinationFolder(): bool
    {
        if (!is_dir($this->destinationFolder) || !is_writable($this->destinationFolder)) {
            throw new InvalidArgumentException("The folder {$this->destinationFolder} for {$this->media->id} is either invalid or not writable");
        }

        return true;
    }

    /**
     * This function will generate full command line to be used to grab video media.
     */
    protected function buildCommandLine(): void
    {
        $this->commandLine = self::YOUTUBE_DL_BINARY . ' ' . implode(' ', $this->getYoutubeDlParameters());
    }

    /**
     * This function will define the youtube-dl parameters to be used.
     */
    protected function youtubeDlParameters(): void
    {
        $this->youtubeDlparameters = [
            '--no-warnings', // Ignore warnings
            '--extract-audio', // Convert video files to audio-only files (requires ffmpeg)
            '--audio-format ' . self::AUDIO_FORMAT, // post processing option to convert file obtained to mp3
            // "--format 'bestaudio[ext=mp3]/best[ext=webm]/best'", // Download best (else dl is slow) //
            "--output '" . $this->destinationFolder . "%(id)s.%(ext)s'",
        ];

        if (!$this->verbose) {
            $this->youtubeDlparameters[] = '--quiet';
        }

        $this->youtubeDlparameters[] = 'https://www.youtube.com/watch?v=' . $this->media->media_id;

        if (!$this->verbose) {
            $this->youtubeDlparameters[] = '>/dev/null 2>&1';
        }
    }

    /**
     * clean the way before.
     * remove mp3 and video previously generated.
     */
    protected function cleanPreviouslyDownloaded()
    {
        if (file_exists($this->downloadedFilePath())) {
            unlink($this->downloadedFilePath());
        }

        if (file_exists($this->downloadedVideoFilePath())) {
            unlink($this->downloadedVideoFilePath());
        }

        return true;
    }
}
