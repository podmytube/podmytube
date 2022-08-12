<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Analytics\LogLineParser;
use App\Analytics\LogProcessor;
use App\Models\Channel;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function (): void {
    $this->fakeLogFilepath = 'tmp/logfile.log';
});

it('unknown channel should fail (from file)', function (): void {
    $unknownChannelId = 'unknown_channel';
    $expectedNbLinesToProcess = 2;

    // creating fake log file with one log line.
    createFakeLogFile(
        filePath: $this->fakeLogFilepath,
        fileContent: createFakeLogLines(
            date: now(),
            channelId: $unknownChannelId,
            mediaId: 'unknown_media',
            nblines: $expectedNbLinesToProcess,
            status: 304,
            bytesSent: 0,
        )
    );
    $logProcessor = LogProcessor::withFile($this->fakeLogFilepath)->process();

    expect(false)->toBe($logProcessor->hasChannelBeenMet($unknownChannelId));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect(0)->toBe($logProcessor->nbValidLines());
});

it('known channel with unknown media should fail (from file)', function (): void {
    $channel = Channel::factory()->create();
    $expectedNbLinesToProcess = 2;

    // creating fake log file with one log line.
    createFakeLogFile(
        filePath: $this->fakeLogFilepath,
        fileContent: createFakeLogLines(
            date: now(),
            channelId: $channel->channel_id,
            mediaId: 'unknown_media',
            nblines: $expectedNbLinesToProcess,
            status: 304,
            bytesSent: 0,
        )
    );
    $logProcessor = LogProcessor::withFile($this->fakeLogFilepath)->process();

    expect(true)->toBe($logProcessor->hasChannelBeenMet($channel->channel_id));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect(0)->toBe($logProcessor->nbValidLines());
});

it('known media of known channel with status 304 should not be considered downloaded (from file)', function (): void {
    $channel = Channel::factory()->create();
    $media = Media::factory()->create();

    $expectedNbLinesToProcess = 2;

    // creating fake log file with one log line.
    createFakeLogFile(
        filePath: $this->fakeLogFilepath,
        fileContent: createFakeLogLines(
            date: now(),
            channelId: $channel->channel_id,
            mediaId: $media->media_id,
            nblines: $expectedNbLinesToProcess,
            status: 304,
            bytesSent: 0,
        )
    );
    $logProcessor = LogProcessor::withFile($this->fakeLogFilepath)->process();

    expect(true)->toBe($logProcessor->hasChannelBeenMet($channel->channel_id));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbValidLines());
});

it('known media of known channel with status 200 but small bytes sent should not be considered downloaded (from file)', function (): void {
    $channel = Channel::factory()->create();
    $media = Media::factory()->create();

    $expectedNbLinesToProcess = 2;

    // creating fake log file with one log line.
    createFakeLogFile(
        filePath: $this->fakeLogFilepath,
        fileContent: createFakeLogLines(
            date: now(),
            channelId: $channel->channel_id,
            mediaId: $media->media_id,
            nblines: $expectedNbLinesToProcess,
            status: 304,
            bytesSent: (int) round($media->weight() * 0.10, 0, PHP_ROUND_HALF_DOWN),
        )
    );
    $logProcessor = LogProcessor::withFile($this->fakeLogFilepath)->process();

    expect(true)->toBe($logProcessor->hasChannelBeenMet($channel->channel_id));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbValidLines());
});

it('known media of known channel with status 200 and 80% bytes sent should be considered downloaded (from file)', function (): void {
    $channel = Channel::factory()->create();
    $media = Media::factory()->create();

    $expectedNbLinesToProcess = 2;

    // creating fake log file with one log line.
    createFakeLogFile(
        filePath: $this->fakeLogFilepath,
        fileContent: createFakeLogLines(
            date: now(),
            channelId: $channel->channel_id,
            mediaId: $media->media_id,
            nblines: $expectedNbLinesToProcess,
            status: 304,
            bytesSent: (int) round($media->weight() * 0.80, 0),
        )
    );
    $logProcessor = LogProcessor::withFile($this->fakeLogFilepath)->process();

    expect(true)->toBe($logProcessor->hasChannelBeenMet($channel->channel_id));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbValidLines());
});

it('known media of known channel with status 200 fully downloaded should be considered downloaded (from file)', function (): void {
    $channel = Channel::factory()->create();
    $media = Media::factory()->grabbedAt(now()->subDay())->create();
    $expectedNbLinesToProcess = 2;
    $expectedChannelDownload = $expectedMediaDownload = $expectedNbLinesToProcess;

    // creating fake log file with one log line.
    createFakeLogFile(
        filePath: $this->fakeLogFilepath,
        fileContent: createFakeLogLines(
            date: now(),
            channelId: $channel->channel_id,
            mediaId: $media->media_id,
            nblines: $expectedNbLinesToProcess,
            status: 200,
            bytesSent: $media->weight(),
        )
    );
    $logProcessor = LogProcessor::withFile($this->fakeLogFilepath)->process();

    expect(true)->toBe($logProcessor->hasChannelBeenMet($channel->channelId()));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbValidLines());

    expect($expectedChannelDownload)->toBe($logProcessor->nbDownloadsByChannel($channel, now()));
    expect($expectedMediaDownload)->toBe($logProcessor->nbDownloadsByMedia($media, now()));
});

it('known media of known channel with status 200 fully downloaded should be considered downloaded (from string)', function (): void {
    $channel = Channel::factory()->create();
    $media = Media::factory()->grabbedAt(now()->subDay())->create();
    $expectedNbLinesToProcess = 2;
    $expectedChannelDownload = $expectedMediaDownload = $expectedNbLinesToProcess;

    // creating fake log file with one log line.
    $logProcessor = LogProcessor::with(
        createFakeLogLines(
            date: now(),
            channelId: $channel->channel_id,
            mediaId: $media->media_id,
            nblines: $expectedNbLinesToProcess,
            status: 200,
            bytesSent: $media->weight(),
        )
    )->process();

    expect(true)->toBe($logProcessor->hasChannelBeenMet($channel->channelId()));
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbProcessedLines());
    expect($expectedNbLinesToProcess)->toBe($logProcessor->nbValidLines());

    expect($expectedChannelDownload)->toBe($logProcessor->nbDownloadsByChannel($channel, now()));
    expect($expectedMediaDownload)->toBe($logProcessor->nbDownloadsByMedia($media, now()));
});

/*
|--------------------------------------------------------------------------
| helpers & providers
|--------------------------------------------------------------------------
*/

function createFakeLogFile(
    string $filePath,
    string $fileContent
): void {
    file_put_contents($filePath, $fileContent);
}

function createFakeLogLines(
    Carbon $date,
    string $channelId,
    string $mediaId,
    ?int $status = 200,
    ?int $bytesSent = 0,
    ?int $nblines = 1
): string {
    $logLines = '';
    for ($index = 0; $index < $nblines; $index++) {
        if ($index !== 0) {
            $logLines .= PHP_EOL;
        }
        // this kind of log line is from docker logs FILE
        // $logLines .= '{"log":"172.18.0.4 - - [' . $date->format(LogLineParser::DATE_FORMAT) . '] \"GET /' . $channelId . '/' . $mediaId . '.mp3 HTTP/1.1\" ' . $status . ' ' . $bytesSent . ' \"-\" \"AppleCoreMedia/1.0.0.19G71 (iPhone; U; CPU OS 15_6 like Mac OS X; fr_fr)\" \"164.160.136.26, 197.234.242.64\"\n","stream":"stdout","time":"2022-08-05T22:02:18.609935235Z"}';
        $logLines .= '172.18.0.3 - - [' . $date->format(LogLineParser::DATE_FORMAT) . '] "GET /' . $channelId . '/' . $mediaId . '.mp3 HTTP/1.1" ' . $status . ' ' . $bytesSent . ' "-" "GuzzleHttp/6.1.0 curl/7.68.0 PHP/7.4.9" "149.28.73.203, 172.70.210.36"';
    }

    return $logLines;
}
