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
use App\Plan;
use App\Services\YoutubeChannelCheckingService;
use App\Subscription;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Madcoda\Youtube\Youtube;

class ChannelCreateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
    public function store(Request $request)
    {
        try {
            /**
             * The only field required is the channel id. We are asking for the url channel
             * It should be 26 characters long too contain at least http://youtube.com/channel/
             */
            $request->validate([
                'channel_url' => 'required|string|min:27',
            ]);

            $channelId = Channel::extractChannelIdFromUrl(
                $request->channel_url
            );

            /**
             * Getting current authenticated user
             */
            $user = Auth::user();

            /**
             * get youtube obj
             */
            $youtubeObj = new Youtube([
                'key' => ApiKey::make()->get(),
            ]);

            /**
             * Getting basic channel informations
             */
            $channelName = YoutubeChannelCheckingService::init(
                $youtubeObj,
                $channelId
            )->getChannelName();

            /**
             * Channel creating
             */
            try {
                $channel = Channel::create([
                    'user_id' => $user->user_id,
                    'channel_id' => $channelId,
                    'channel_name' => $channelName,
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
                    'channel' => $channelName,
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
             */

            $request->session()->flash('message', $exception->getMessage());
            $request->session()->flash('messageClass', 'alert-danger');
        } finally {
            return redirect()->route('home');
        }
    }
}
