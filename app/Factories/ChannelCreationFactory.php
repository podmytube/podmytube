<?php

namespace App\Factories;

use App\Channel;
use App\Events\ChannelRegistered;
use App\Exceptions\YoutubeChannelIdDoesNotExistException;
use App\Modules\YoutubeChannelId;
use App\Plan;
use App\Subscription;
use App\User;
use App\Youtube\YoutubeChannel;
use Illuminate\Support\Facades\DB;

class ChannelCreationFactory
{
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
            $this->plan = Plan::find(Plan::FREE_PLAN_ID);
        }

        /** extract channel id from url */
        $this->channelId = YoutubeChannelId::fromUrl($youtubeUrl)->get();

        /** check if channel exists in youtube */
        $youtubeChannel = new YoutubeChannel();
        if (!$youtubeChannel->forChannel($this->channelId)->exists()) {
            throw new YoutubeChannelIdDoesNotExistException("This channel id {$this->channelId} does not exists on youtube.");
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

    protected function checkYoutubeChannelExists()
    {
        /**
         * check channel exists
         */
        $result = ($this->youtubeChannelObj = new YoutubeChannel())
            ->forChannel($channelId)
            ->exists();

        /**
         * Update quota usage
         */
        $this->updateQuotaConsumption();
    }
}
