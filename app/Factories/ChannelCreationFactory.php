<?php

namespace App\Factories;

use App\Category;
use App\Channel;
use App\Events\ChannelRegistered;
use App\Exceptions\ChannelAlreadyRegisteredException;
use App\Exceptions\YoutubeChannelIdDoesNotExistException;
use App\Exceptions\YoutubeNoResultsException;
use App\Modules\YoutubeChannelId;
use App\Plan;
use App\Subscription;
use App\User;
use App\Youtube\YoutubeChannel;
use Illuminate\Support\Facades\DB;

class ChannelCreationFactory
{
    public const DEFAULT_PLAN_SLUG = 'forever_free';
    public const DEFAULT_CATEGORY_SLUG = 'society-culture';

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\User $user */
    protected $user;

    /** @var \App\Plan $plan */
    protected $plan;

    /** @var string $channel_id */
    protected $channel_id;

    private function __construct(User $user, string $youtubeUrl, Plan $plan)
    {
        $this->user = $user;
        $this->plan = $plan;

        /** extract channel id from url */
        $this->channel_id = YoutubeChannelId::fromUrl($youtubeUrl)->get();

        /** check if channel exists in youtube */
        $youtubeChannel = new YoutubeChannel();
        try {
            $youtubeChannel->forChannel($this->channel_id)->exists();
        } catch (YoutubeNoResultsException $exception) {
            throw new YoutubeChannelIdDoesNotExistException("This youtube channel ({$youtubeUrl}) does not exists on youtube 😔.");
        }

        /** check if channel has already been registered */
        $channelExist = Channel::byChannelId($this->channel_id);
        if ($channelExist !== null) {
            throw new ChannelAlreadyRegisteredException("This youtube channel ({$youtubeUrl}) has already been registered 🤔️.");
        }

        DB::transaction(function () use ($youtubeChannel) {
            /** Creating channel model */
            $this->channel = Channel::create([
                'user_id' => $this->user->id(),
                'channel_id' => $this->channel_id,
                'channel_name' => $youtubeChannel->name(),
                'category_id' => Category::bySlug(self::DEFAULT_CATEGORY_SLUG)->id,
            ]);

            /** Creating subscription for channel */
            Subscription::create([
                'channel_id' => $this->channel_id,
                'plan_id' => $this->plan->id,
            ]);
        });

        event(new ChannelRegistered($this->channel));
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function channel()
    {
        return $this->channel;
    }

    public function user()
    {
        return $this->user;
    }
}
