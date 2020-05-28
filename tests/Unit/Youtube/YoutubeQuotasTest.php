<?php

namespace Tests\Unit\Youtube;

use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeCore;
use App\Youtube\YoutubePlaylists;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeQuotasTest extends TestCase
{
    const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
        $this->quotaCalculator = new YoutubeQuotas();
    }

    public function testMinimalChannelListShouldBeOk()
    {
        $expectedQuota = 1;

        $this->assertEquals(
            $expectedQuota,
            YoutubeChannel::init($this->quotaCalculator)
                ->forChannel(
                    YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID,
                    $parts = ['id']
                )
                ->quotasUsed()
        );
    }

    public function testChannelListWithSomePartsShouldBeOk()
    {
        $expectedQuota = 7;
        // base(1) + id(0) + snippet(2) + contentDetails(2) + status(2)
        $this->assertEquals(
            $expectedQuota,
            YoutubeChannel::init($this->quotaCalculator)
                ->forChannel(
                    YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID,
                    $parts = ['id', 'snippet', 'contentDetails', 'status']
                )
                ->quotasUsed()
        );
    }

    /*  public function testChannelListWithAllPartsShouldBeOk()
    {
        $expectedQuota = 21;
        $this->assertEquals(
            $expectedQuota,
            YoutubeChannel::init($this->quotaCalculator)
                ->forChannel(
                    YoutubeCoreTest::PEWDIEPIE_CHANNEL_ID,
                    $parts = [
                        'auditDetails',
                        'brandingSettings',
                        'contentDetails',
                        'contentOwnerDetails',
                        'id',
                        'localizations',
                        'snippet',
                        'statistics',
                        'status',
                        'topicDetails',
                    ]
                )
                ->quotasUsed()
        );
    } */
}
