<?php

namespace Tests\Unit\Youtube;

use App\ApiKey;
use App\Youtube\YoutubeCore;
use App\Youtube\YoutubeQuotas;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class YoutubeQuotasTest extends TestCase
{
    const PEWDIEPIE_CHANNEL_ID = 'UC-lHJZR3Gqxm24_Vd_AJ5Yw';
    protected $apikey;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'ApiKeysTableSeeder']);
    }

    public function testMinimalChannelListShouldBeOk()
    {
        $expectedQuota = 1;
        $youtubeCore = YoutubeCore::init()
            ->defineEndpoint('channels.list')
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->addParts(['id']);

        $this->assertEquals(
            $expectedQuota,
            YoutubeQuotas::init($youtubeCore)->quotaUsed()
        );
    }

    public function testChannelListWithSomePartsShouldBeOk()
    {
        $expectedQuota = 7;
        $youtubeCore = YoutubeCore::init()
            ->defineEndpoint('channels.list')
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->addParts(['id', 'snippet', 'auditDetails']);
        // base(1) + id(0) + snippet(2) + auditDetails(4)
        $this->assertEquals(
            $expectedQuota,
            YoutubeQuotas::init($youtubeCore)->quotaUsed()
        );
    }

    public function testChannelListWithAllPartsShouldBeOk()
    {
        $expectedQuota = 21;
        $youtubeCore = YoutubeCore::init()
            ->defineEndpoint('channels.list')
            ->addParams(['id' => self::PEWDIEPIE_CHANNEL_ID])
            ->addParts([
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
            ]);
        $this->assertEquals(
            $expectedQuota,
            YoutubeQuotas::init($youtubeCore)->quotaUsed()
        );
    }
}
