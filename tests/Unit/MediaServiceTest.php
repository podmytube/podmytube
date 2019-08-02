<?php

namespace Tests\Unit;

use App\Channel;
use App\Media;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Services\MediaService;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{
    protected $curMonth;
    protected $faker;

    public function setUp(): void
    {
        $this->curMonth = (int) date('m');
        $this->faker = Faker::create();
        parent::setUp();
    }

    protected function getStartDate()
    {
        $startDate = carbon::createMidnightDate(date('Y'), date('m'), 1);
    }

    protected function getEndDate()
    {
        return carbon::createFromDate(date('Y'), date('m'), date('d'));
    }

    public function testGetMediasStatusByPeriodForFreeChannel()
    {
        $startDate = carbon::createMidnightDate(date('Y'), date('m'), 1);
        
        $expectedResults = [
            "YsBVu6f8pR8" => 1,
            "KsSPMDe_YWY" => 1,
            "hKjtoNByLAI" => 0,
        ];
        $results = MediaService::getMediasStatusByPeriodForChannel(Channel::find('freeChannel'), date('m'), date('Y'));
        foreach ($results as $result) {
            $this->assertEquals(
                $expectedResults[$result->media_id],
                $result->grabbed,
                "For channel {freeChannel} media {{$result->media_id}} grabbed status should be {{$expectedResults[$result->media_id]}} and is equal to {{$result->grabbed}}"
            );
        }
        $this->assertCount(count($expectedResults), $results->toArray());
    }

    public function testGetMediasStatusWithWrongPeriodShouldFail()
    {
        $this->expectException(\Exception::class);
        $results = MediaService::getMediasStatusByPeriodForChannel(Channel::find('freeChannel'), 1555);
    }

    public function testGetGrabbedMediaForFreeChannelShouldReturn2()
    {
        $expectedNumberOfVideosDownloaded = 2;
        $result = MediaService::getNbEpisodesAlreadyDownloadedThisMonth(Channel::find('freeChannel'));
        $this->assertEquals(
            $expectedNumberOfVideosDownloaded,
            $result,
            "For channel {freeChannel} we should have grabbed {{$expectedNumberOfVideosDownloaded}} videos by the way we grabbed {{$result}}"
        );
    }

    public function testGetGrabbedMediasFor()
    {
        $expectedMediaIdsDownloaded = ['YsBVu6f8pR8', 'KsSPMDe_YWY'];
        $result = (MediaService::getGrabbedMediasFor(Channel::find('freeChannel'), $this->curMonth))->pluck('media_id')->toArray();
        $this->assertEqualsCanonicalizing(
            $expectedMediaIdsDownloaded,
            $result,
            "For channel {freeChannel} we should have grabbed {" . implode(', ', $expectedMediaIdsDownloaded) . "} and we received {" . implode(', ', $result) . "}"
        );
    }

    public function testGetPublishedMediasFor()
    {
        $expectedMediaIdsPublished = ['YsBVu6f8pR8', 'KsSPMDe_YWY', 'hKjtoNByLAI'];
        $result = (MediaService::getPublishedMediasFor(Channel::find('freeChannel'), $this->curMonth))->pluck('media_id')->toArray();
        $this->assertEqualsCanonicalizing(
            $expectedMediaIdsPublished,
            $result,
            "For channel {freeChannel} published videos are {" . implode(', ', $expectedMediaIdsPublished) . "} and we received {" . implode(', ', $result) . "}"
        );
    }

    public function testInvalidMonthShouldThrowOneException()
    {
        $this->expectException(\Exception::class);
        $result = MediaService::getPublishedMediasFor(Channel::find('freeChannel'), 0);
    }
}
