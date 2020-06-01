<?php

/**
 * the channel create controller
 *
 * this controller is handling the new channel form part.
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\ApiKey;
use App\Channel;
use App\Events\ChannelRegistered;
use App\Exceptions\ChannelCreationHasFailedException;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\SubscriptionHasFailedException;
use App\Http\Requests\ChannelCreationRequest;
use App\Modules\YoutubeChannelId;
use App\Plan;
use App\Quota;
use App\Subscription;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeQuotas;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class ChannelCreateController extends Controller
{
    /** @var App\Youtube\YoutubeChannel $youtubeChannelObj */
    protected $youtubeChannelObj;

    /**
     * Display the form channel creation
     *
     * @return void
     */

    public function create()
    {
        $plans = [
            'free' => Plan::FREE_PLAN_ID,
            'weekly' => Plan::WEEKLY_PLAN_ID,
            'daily' => Plan::DAILY_PLAN_ID,
        ];
        return view('channel.create', compact('plans'));
    }

    /**
     * create one channel from the form received
     *
     * @return void
     */
    public function store(ChannelCreationRequest $request)
    {
        try {
            $validatedParams = $request->validated();
            $channelId = YoutubeChannelId::fromUrl(
                $validatedParams['channel_url']
            )->get();

            /**
             * check channel exists
             */
            ($this->youtubeChannelObj = new YoutubeChannel())
                ->forChannel($channelId)
                ->exists();

            /**
             * Update quota usage
             */
            $this->updateQuotaConsumption();

            /**
             * Channel creating
             */
            try {
                $channel = Channel::create([
                    'user_id' => Auth::id(),
                    'channel_id' => $channelId,
                    'channel_name' => $this->youtubeChannelObj->name(),
                ]);
            } catch (QueryException $exception) {
                throw new ChannelCreationHasFailedException(
                    $exception->getMessage()
                );
            }

            /**
             * Creating subscription on one free plan (default)
             * We will update it once paid.
             */
            try {
                Subscription::create([
                    'channel_id' => $channelId,
                    'plan_id' => Plan::FREE_PLAN_ID,
                ]);
            } catch (QueryException $exception) {
                throw new SubscriptionHasFailedException(
                    $exception->getMessage()
                );
            }

            event(new ChannelRegistered($channel));

            /**
             * All went fine
             */
            $request->session()->flash(
                'message',
                __('messages.flash_channel_has_been_created', [
                    'channel' => $this->youtubeChannelObj->name(),
                ])
            );
            $request->session()->flash('messageClass', 'alert-success');
        } catch (ChannelCreationInvalidUrlException | ChannelCreationInvalidChannelUrlException $exception) {
            $request
                ->session()
                ->flash('message', __('messages.flash_channel_id_is_invalid'));
            $request->session()->flash('messageClass', 'alert-danger');
        } catch (\Exception $exception) {
            /**
             * will catch
             * - SubscriptionHasFailedException
             * - ChannelCreationHasFailedException
             * - YoutubeNoResultsException
             */

            $request->session()->flash('message', $exception->getMessage());
            $request->session()->flash('messageClass', 'alert-danger');
        } finally {
            return redirect()->route('home');
        }
    }

    /**
     * will persist quota consumption.
     */
    protected function updateQuotaConsumption()
    {
        $apikeysAndQuotas = YoutubeQuotas::forUrls(
            $this->youtubeChannelObj->queriesUsed()
        )->quotaConsumed();
        array_walk($apikeysAndQuotas, function ($quota, $apikey) {
            Quota::create([
                'apikey_id' => ApiKey::where('apikey', '=', $apikey)->first()
                    ->id,
                'script' => pathinfo(__FILE__, PATHINFO_BASENAME),
                'quota_used' => $quota,
                'created_at' => Carbon::now(),
            ]);
        });
    }
}
