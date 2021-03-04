<?php

namespace Tests;

use App\Channel;
use App\Media;
use App\Plan;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    /** some channels */
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const NOWTECH_CHANNEL_ID = 'UCVwG9JHqGLfEO-4TkF-lf2g';
    public const NOWTECH_LIVE_CHANNEL_ID = 'UCRU38zigLJNtMIh7oRm2hIg';

    /** this video does exist and has two tags ['dev', 'podmytube'] */
    protected const BEACH_VOLLEY_VIDEO_1 = 'EePwbhMqEh0';
    /** this video does exist and has no tag */
    protected const BEACH_VOLLEY_VIDEO_2 = '9pTBAkkTRbw';
    /** this video is the shortest I know */
    protected const MARIO_COIN_VIDEO = 'qfx6yf8pux4';

    /** some playlists */
    public const NOWTECH_LEMUG_YOUTUBE_PLAYLIST_ID = 'PLhQHoIKUR5vD0vq6Jwns89QAz9OZWTvpx';

    public const PODMYTUBE_TEST_PLAYLIST_ID = 'PLyeI3mV1fCpovDzuc8gRaWh2HysiVaoBQ'; // to be removed this one is on my second born channel (MISTAKE)

    protected function addMediasToChannel(Channel $channel, int $numberOfMediasToAdd = 1, bool $grabbed = false)
    {
        $medias = factory(Media::class, $numberOfMediasToAdd)->create(
            [
                'channel_id' => $channel->channel_id,
                'grabbed_at' => $grabbed == true ? $this->faker->dateTimeBetween(Carbon::now()->startOfMonth(), Carbon::now()) : null,
            ]
        );

        return $medias->count() == 1 ? $medias->first() : $medias;
    }

    /**
     * create one channel
     */
    protected function createChannelWithPlan(Plan $plan = null) : \App\Channel
    {
        $createContext = [];
        if ($plan) {
            $createContext = ['plan_id' => $plan->id];
        }
        return factory(Subscription::class)->create($createContext)->channel;
    }

    /**
     * Laravel is encoding.
     * So i'm encoding the same way to be sure tests will stay green.
     * By example "d'angelo" => "d&#039;angelo"
     */
    public function stringEncodingLikeLaravel(string $str)
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML401);
    }
}
