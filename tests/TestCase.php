<?php

namespace Tests;

use App\Channel;
use App\Interfaces\Coverable;
use App\Media;
use App\Plan;
use App\Subscription;
use App\Thumb;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    /** some channels */
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const NOWTECH_CHANNEL_ID = 'UCVwG9JHqGLfEO-4TkF-lf2g';
    public const NOWTECH_LIVE_CHANNEL_ID = 'UCRU38zigLJNtMIh7oRm2hIg';
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';

    /** billing */
    public const BILLING_ONLY_MONTHLY = 0;
    public const BILLING_ONLY_YEARLY = 1;
    public const BILLING_BOTH = 2;

    /** this video does exist and has two tags ['dev', 'podmytube'] */
    protected const BEACH_VOLLEY_VIDEO_1 = 'EePwbhMqEh0';
    /** this video does exist and has no tag */
    protected const BEACH_VOLLEY_VIDEO_2 = '9pTBAkkTRbw';
    /** this video is the shortest I know */
    protected const MARIO_COIN_VIDEO = 'qfx6yf8pux4';
    protected const MARIO_MUSHROOM_VIDEO = '6G-k4zxou7Y';

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
    protected function createChannelWithPlan(Plan $plan = null): Channel
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
     * IE "d'angelo" => "d&#039;angelo"
     */
    public function stringEncodingLikeLaravel(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML401);
    }

    public function createChannelForUser(?User $user = null): Channel
    {
        $createContext = [];
        if ($user !== null) {
            $createContext = ['user_id' => $user->user_id];
        }
        return factory(Channel::class)->create($createContext);
    }

    public function createRealThumbFileFor(Coverable $coverable):  Thumb
    {
        $thumb = factory(Thumb::class)->create([
            'coverable_type' => get_class($coverable),
            'coverable_id' => $coverable->id(),
        ]);
        $this->createFakeCoverFor($thumb);
        return $thumb;
    }

    /** will create a cover from existing fixture and return filesize */
    public function createFakeCoverFor(Thumb $thumb): int
    {
        /** create channel folder */
        $fileName = $thumb->file_name;
        $filePath = $thumb->coverable->channelId() . '/' . $fileName;
        Storage::disk(Thumb::LOCAL_STORAGE_DISK)
            ->put(
                $filePath,
                file_get_contents(base_path('tests/fixtures/images/sampleThumb.jpg'))
            );
        return Storage::disk(Thumb::LOCAL_STORAGE_DISK)->size($filePath);
    }

    public function createChannel(?User $user = null, ?Plan $plan = null): Channel
    {
        /** if owner specified */
        if ($user !== null) {
            $userContext = ['user_id' => $user->id()];
        }
        $channel = factory(Channel::class)->create($userContext);

        /** if no plan, affecting a created one */
        if ($plan === null) {
            $plan = factory(Plan::class)->create();
        }
        $channel->subscribeToPlan($plan);
        $channel->refresh();
        return $channel;
    }
}
