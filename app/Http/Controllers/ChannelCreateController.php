<?php

/**
 * the channel create controller
 *
 * this controller is handling the new channel form part.
 *
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Channel;
use App\Exceptions\ChannelCreationHasFailedException;
use App\Plan;
use App\Subscription;
use App\Mail\ChannelIsRegistered;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\SubscriptionHasFailedException;
use App\Jobs\MailChannelIsRegistered;
use App\Services\YoutubeChannelCheckingService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            'free' => Plan::_FREE_PLAN_ID,
            'weekly' => Plan::_WEEKLY_PLAN_ID,
            'daily' => Plan::_DAILY_PLAN_ID,
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

            $channelId = Channel::extractChannelIdFromUrl($request->channel_url);
            
            /**
             * Getting current authenticated user
             */
            $user = Auth::user();

            /**
             * Getting same basic channel informations
             */
            $channelName = YoutubeChannelCheckingService::getChannelName($channelId);

            /**
             * Channel creating
             */
            try {
                $channel = Channel::create([
                    'user_id' => $user->user_id,
                    'channel_id' => $channelId,
                    'channel_name' => $channelName,
                ]);
            } catch (QueryException $e) {
                throw new ChannelCreationHasFailedException($e->getMessage());
            }


            /**
             * Creating subscription on one free plan (default)
             * We will update it once paid.
             */
            try {
                Subscription::create([
                    'channel_id' => $channelId,
                    'plan_id' => Plan::_FREE_PLAN_ID,
                ]);
            } catch (QueryException $e) {
                throw new SubscriptionHasFailedException($e->getMessage());
            }

            /** Sending the channel registered mail within the queue */
            MailChannelIsRegistered::dispatchNow($channel);

            /**
             * All went fine
             */
            $request->session()->flash('message', __('messages.flash_channel_has_been_created', ['channel' => $channelName]));
            $request->session()->flash('messageClass', 'alert-success');
        } catch (ChannelCreationInvalidUrlException | ChannelCreationInvalidChannelUrlException $e) {
            $request->session()->flash('message', __('messages.flash_channel_id_is_invalid'));
            $request->session()->flash('messageClass', 'alert-danger');
        } catch (\Exception $e) {
            /**
             * will catch 
             * - SubscriptionHasFailedException 
             * - ChannelCreationHasFailedException
             */
            $request->session()->flash('message', $e->getMessage());
            $request->session()->flash('messageClass', 'alert-danger');
        } finally {
            return redirect()->route('home');
        }
    }
}
