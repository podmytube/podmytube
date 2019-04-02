<?php

namespace Tests\Unit;

use App\Channel;
use App\Services\MediaService;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{

    public function testGetGrabbedMediaForFreeChannelShouldReturn2()
    {
        $expectedNumberOfVideosDownloaded = 2;
        $channel = Channel::find('freeChannel');
        $result = MediaService::getNbEpisodesAlreadyDownloadedThisMonth($channel);
        $this->assertEquals(
            $expectedNumberOfVideosDownloaded,
            $result,
            "For channel {freeChannel} we should have grabbed {{$expectedNumberOfVideosDownloaded}} videos by the way we grabbed {{$result}}"
        );
    }

    public function testGetGrabbedMediasFor()
    {
        $expectedMediaIdsDownloaded = ['YsBVu6f8pR8','KsSPMDe_YWY'];
        $channel = Channel::find('freeChannel');
        $result = (MediaService::getGrabbedMediasFor($channel,date('m')))->pluck('media_id')->toArray();        
        $this->assertEqualsCanonicalizing(
            $expectedMediaIdsDownloaded,
            $result,
            "For channel {freeChannel} we should have grabbed {".implode(', ', $expectedMediaIdsDownloaded)."} and we received {".implode(', ', $result)."}");
    }
    
    public function testGetPublishedMediasFor()
    {
        $expectedMediaIdsPublished = ['YsBVu6f8pR8','KsSPMDe_YWY','hKjtoNByLAI','Aks6eKumi3c'];
        $channel = Channel::find('freeChannel');
        $result = (MediaService::getPublishedMediasFor($channel,date('m')))->pluck('media_id')->toArray();        

        $this->assertEqualsCanonicalizing(
            $expectedMediaIdsPublished,
            $result,
            "For channel {freeChannel} published videos are {".implode(', ', $expectedMediaIdsPublished)."} and we received {".implode(', ', $result)."}");
    }
}
