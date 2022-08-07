<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Analytics\LogLineParser;
use App\Analytics\LogProcessor;
use App\Exceptions\LogProcessorUnknownChannelException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function (): void {
    $this->mp3SampleLogFile = $this->getFixturesPath('Logs/mp3-sample.log');
});

/* it('should process all lines', function (): void {
    $logProcessor = LogProcessor::with($this->mp3SampleLogFile)->process();

    $expectedNumberOfProcessedLines = 2553; // number of line in sample
    $expectedNumberOfValidLines = 2518;
    expect($expectedNumberOfProcessedLines)->toBe($logProcessor->nbProcessedLines());

    expect($expectedNumberOfValidLines)->toBe($logProcessor->nbValidLines());
}); */

it('unknown channel should throw exception and be marked as unknown', function (): void {
    $unknownChannelId = 'unknown_channel';
    $logLine = createFakeLogLine(date: now(), channelId: $unknownChannelId, mediaId: 'unknown_media');

    $logProcessor = LogProcessor::with();
    $logProcessor->processLine($logLine);
})->throws(LogProcessorUnknownChannelException::class);

/*
|--------------------------------------------------------------------------
| helpers & providers
|--------------------------------------------------------------------------
*/

function createFakeLogLine(Carbon $date, string $channelId, string $mediaId, int $status = 200, int $mediaWeight = 0): string
{
    return '{"log":"172.18.0.4 - - [' . $date->format(LogLineParser::DATE_FORMAT) . '] \"GET /' . $channelId . '/' . $mediaId . '.mp3 HTTP/1.1\" ' . $status . ' ' . $mediaWeight . ' \"-\" \"AppleCoreMedia/1.0.0.19G71 (iPhone; U; CPU OS 15_6 like Mac OS X; fr_fr)\" \"164.160.136.26, 197.234.242.64\"\n","stream":"stdout","time":"2022-08-05T22:02:18.609935235Z"}';
}
