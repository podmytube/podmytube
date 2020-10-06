<?php

namespace App\Factories;

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
use Illuminate\Support\Facades\Log;

class ChannelCreationFactory
{
    public const DEFAULT_PLAN_SLUG = 'forever_free';

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\User $user */
    protected $user;

    /** @var \App\Plan $plan */
    protected $plan;

    /** @var string $channel_id */
    protected $channel_id;

    private function __construct(User $user, string $youtubeUrl, Plan $plan = null)
    {
        Log::debug('==================================================================');
        Log::debug(__CLASS__ . '::' . __FUNCTION__);
        Log::debug($user);
        Log::debug($youtubeUrl);
        $this->user = $user;
        $this->plan = $plan;
        /** at this time no plan is selectable to choose */
        if ($this->plan === null) {
            $this->plan = Plan::bySlug(self::DEFAULT_PLAN_SLUG);
        }

        Log::debug($this->plan);
        /** extract channel id from url */
        $this->channel_id = YoutubeChannelId::fromUrl($youtubeUrl)->get();
        Log::debug("Channel id : {$this->channel_id}");

        /** check if channel exists in youtube */
        $youtubeChannel = new YoutubeChannel();
        try {
            $youtubeChannel->forChannel($this->channel_id)->exists();
        } catch (YoutubeNoResultsException $exception) {
            throw new YoutubeChannelIdDoesNotExistException("This channel id {$this->channel_id} does not exists on youtube.");
        }
        Log::debug('youtube channel name : ' . $youtubeChannel->name());
        $channelExist = Channel::byChannelId($this->channel_id);
        if ($channelExist !== null) {
            throw new ChannelAlreadyRegisteredException("The channel {{$channelExist->channel_name}} with id {{$this->channel_id}} is already registered.");
        }

        DB::transaction(function () use ($youtubeChannel) {
            /** Creating channel model */
            $this->channel = Channel::create([
                'user_id' => $this->user->id(),
                'channel_id' => $this->channel_id,
                'channel_name' => $youtubeChannel->name(),
            ]);

            Log::debug('channel created');
            Log::debug('channel_id :' . $this->channel->channel_id);
            Log::debug('plan id :' . $this->plan->id);

            /** Creating subscription for channel */
            Subscription::create([
                'channel_id' => $this->channel_id,
                'plan_id' => $this->plan->id,
            ]);
            Log::debug('subscription created');
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
