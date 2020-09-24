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

class ChannelCreationFactory
{
    public const DEFAULT_PLAN_SLUG = 'forever_free';

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \App\User $user */
    protected $user;

    /** @var \App\Plan $plan */
    protected $plan;

    /** @var string $channelId */
    protected $channelId;

    private function __construct(User $user, string $youtubeUrl, Plan $plan = null)
    {
        $this->user = $user;
        $this->plan = $plan;
        /** at this time no plan is selectable to choose */
        if ($this->plan === null) {
            $this->plan = Plan::bySlug(self::DEFAULT_PLAN_SLUG);
        }

        /** extract channel id from url */
        $this->channelId = YoutubeChannelId::fromUrl($youtubeUrl)->get();

        /** check if channel exists in youtube */
        $youtubeChannel = new YoutubeChannel();
        try {
            $youtubeChannel->forChannel($this->channelId)->exists();
        } catch (YoutubeNoResultsException $exception) {
            throw new YoutubeChannelIdDoesNotExistException("This channel id {$this->channelId} does not exists on youtube.");
        }

        $channelExist = Channel::byChannelId($this->channelId);
        if ($channelExist !== null) {
            throw new ChannelAlreadyRegisteredException("This channel id {$this->channelId} is already registered.");
        }

        DB::transaction(function () use ($youtubeChannel) {
            /** Creating channel model */
            $this->channel = Channel::create([
                'user_id' => $this->user->id(),
                'channel_id' => $this->channelId,
                'channel_name' => $youtubeChannel->name(),
            ]);

            /** Creating subscription for channel */
            Subscription::create([
                'channel_id' => $this->channelId,
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
}
