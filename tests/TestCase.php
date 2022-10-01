<?php

declare(strict_types=1);

namespace Tests;

use App\Interfaces\Coverable;
use App\Interfaces\Podcastable;
use App\Models\ApiKey;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Playlist;
use App\Models\Quota;
use App\Models\StripePlan;
use App\Models\Subscription;
use App\Models\Thumb;
use App\Models\User;
use Database\Seeders\ApiKeysTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\PlansTableSeeder;
use Database\Seeders\StripePlansTableSeeder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;

    /** some channels */
    public const PERSONAL_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';
    public const NOWTECH_CHANNEL_ID = 'UCVwG9JHqGLfEO-4TkF-lf2g';
    public const NOWTECH_LIVE_CHANNEL_ID = 'UCRU38zigLJNtMIh7oRm2hIg';
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';

    /** billing */
    public const BILLING_ONLY_MONTHLY = 0;
    public const BILLING_ONLY_YEARLY = 1;
    public const BILLING_BOTH = 2;

    /** some playlists */
    public const NOWTECH_LEMUG_YOUTUBE_PLAYLIST_ID = 'PLhQHoIKUR5vD0vq6Jwns89QAz9OZWTvpx';

    public const PODMYTUBE_TEST_PLAYLIST_ID = 'PLyeI3mV1fCpovDzuc8gRaWh2HysiVaoBQ'; // to be removed this one is on my second born channel (MISTAKE)

    /** this video does exist and has two tags ['dev', 'podmytube'] */
    protected const BEACH_VOLLEY_VIDEO_1 = 'EePwbhMqEh0';

    /** this video does exist and has no tag */
    protected const BEACH_VOLLEY_VIDEO_2 = '9pTBAkkTRbw';

    /** this video is the shortest I know */
    protected const MARIO_COIN_VIDEO = 'qfx6yf8pux4';
    protected const MARIO_MUSHROOM_VIDEO = '6G-k4zxou7Y';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Laravel is encoding.
     * So i'm encoding the same way to be sure tests will stay green.
     * IE "d'angelo" => "d&#039;angelo".
     */
    public function stringEncodingLikeLaravel(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML401);
    }

    public function createCoverFor(Coverable|Channel|Playlist $coverable): Thumb
    {
        $thumb = Thumb::factory()->create([
            'coverable_type' => $coverable->morphedName(),
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
                file_get_contents(fixtures_path('/images/sampleThumb.jpg'))
            )
        ;

        return Storage::disk(Thumb::LOCAL_STORAGE_DISK)->size($filePath);
    }

    public function createChannel(?User $user = null, ?Plan $plan = null): Channel
    {
        // if owner specified
        $userContext = [];
        if ($user !== null) {
            $userContext = ['user_id' => $user->id()];
        }
        $channel = Channel::factory()->create($userContext);

        // if no plan, affecting a created one
        if ($plan === null) {
            $plan = Plan::factory()->create();
        }
        $channel->subscribeToPlan($plan);
        $channel->refresh();

        return $channel;
    }

    /**
     * Associate media (with file uploaded) to channel.
     * This function is creating medias for specified channel.
     * WARNING : each media created is getting one media file uploaded on remote.
     */
    public function createMediaWithFileForChannel(Channel $channel, int $nbMediasToCreate = 1): Collection
    {
        return Media::factory()
            ->count($nbMediasToCreate)
            ->create(['channel_id' => $channel->channelId()])
            ->map(function ($media): Media {
                $this->createFakeRemoteFileForMedia($media);

                return $media;
            })
        ;
    }

    /**
     * will create fake playlist and associate two wedias with it.
     * this playlist has only some medias in ('GJzweq_VbVc', 'AyU4u-iQqJ4', 'hb0Fo1Jqxkc').
     * so I only need to create these ones and set their state to grabbed or not.
     * That's not finished !
     * If I want to get the medias for this playlist
     * - I need to seed api_keys table
     * - I need to use Podcastable::mediasToPublish() or Podcastable::associatedMedias().
     */
    public function createPlaylistWithMedia(): Playlist
    {
        $playlist = Playlist::factory()->create(['youtube_playlist_id' => self::PODMYTUBE_TEST_PLAYLIST_ID]);

        // with some medias
        Media::factory()->grabbedAt(now()->subDay())->create(['media_id' => 'GJzweq_VbVc']);
        Media::factory()->grabbedAt(now()->subDay())->create(['media_id' => 'AyU4u-iQqJ4']);
        Media::factory()->create(['media_id' => 'hb0Fo1Jqxkc']);

        return $playlist;
    }

    public function createFakeRemoteFileForMedia(Media $media): void
    {
        Storage::put(
            $media->remoteFilePath(),
            file_get_contents(fixtures_path('/Audio/l8i4O7_btaA.mp3'))
        );
    }

    public function createFakeRemoteFileForPodcast(Podcastable $podcastable): void
    {
        Storage::put(
            $podcastable->remoteFilePath(),
            file_get_contents(fixtures_path('/lemug.xml'))
        );
    }

    /**
     * @return Collection|Media
     */
    protected function addMediasToChannel(Channel $channel, int $numberOfMediasToAdd = 1, bool $grabbed = false)
    {
        $factory = Media::factory();
        if ($grabbed === true) {
            $factory = $factory->grabbedAt(now());
        }
        $medias = $factory->count($numberOfMediasToAdd)
            ->create(['channel_id' => $channel->channel_id])
        ;

        return $medias->count() == 1 ? $medias->first() : $medias;
    }

    protected function addGrabbedMediasToChannel(Channel $channel, int $numberOfMediasToAdd = 1): void
    {
        $this->addMediasToChannel($channel, $numberOfMediasToAdd, true);
    }

    /**
     * create one channel.
     */
    protected function createChannelWithPlan(Plan $plan = null): Channel
    {
        $createContext = [];
        if ($plan) {
            $createContext = ['plan_id' => $plan->id];
        }

        return Subscription::factory()->create($createContext)->channel;
    }

    protected function createDepletedApiKeys(int $nbkeys = 1): EloquentCollection
    {
        return $this->createApiKeysWithQuotaUsed(
            quotaUsed: Quota::LIMIT_PER_DAY + 1,
            nbkeys: $nbkeys
        );
    }

    protected function createApiKeysWithQuotaUsed(int $quotaUsed, int $nbkeys = 1): EloquentCollection
    {
        return ApiKey::factory()
            ->count($nbkeys)
            ->create()
            ->each(function (ApiKey $apiKey) use ($quotaUsed): void {
                Quota::factory()->create(
                    [
                        'apikey_id' => $apiKey->id,
                        'quota_used' => $quotaUsed,
                    ]
                );
            })
        ;
    }

    protected function seedStripePlans(bool $plansAreRequired = true): void
    {
        if ($plansAreRequired) {
            $this->seedPlans();
        }

        if (!StripePlan::count()) {
            $this->artisan('db:seed', ['--class' => StripePlansTableSeeder::class]);
        }
    }

    /**
     * create many channel.
     */
    protected function createChannelsWithPlan(Plan $plan = null, int $nbChannels = 1): EloquentCollection
    {
        $createContext = [];
        if ($plan) {
            $createContext = ['plan_id' => $plan->id];
        }

        return Subscription::factory()->count($nbChannels)->create($createContext);
    }

    protected function seedApiKeys(): void
    {
        $this->seed(ApiKeysTableSeeder::class);
    }

    protected function seedPlans(): void
    {
        $this->seed(PlansTableSeeder::class);
    }

    protected function seedCategories(): void
    {
        $this->seed(CategoriesTableSeeder::class);
    }

    protected function createMyOwnChannel(Plan $plan): Channel
    {
        $channel = Channel::factory()->create(['channel_id' => self::PERSONAL_CHANNEL_ID]);

        Subscription::factory()
            ->create(
                [
                    'channel_id' => $channel->channelId(),
                    'plan_id' => $plan->id,
                ]
            )
        ;

        return $channel;
    }

    protected function getPlaylistIdFromChannelId(string $channelId): string
    {
        $result = $channelId;
        $result[1] = 'U';

        return $result;
    }
}
