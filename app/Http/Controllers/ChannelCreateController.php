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
use App\Mail\ChannelIsRegistered;
use App\Plan;
use App\Subscription;
use App\User;
use App\Services\YoutubeChannelCheckingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class ChannelCreateController extends Controller
{
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
        /**
         * The only field required is the channel id. We are asking for the url channel
         * It should be 26 characters long too contain at least http://youtube.com/channel/
         */
        $request->validate([
            'channel_url' => 'required|string|min:27',
        ]);

        try {
            $channel_id = Channel::extractChannelIdFromUrl($request);
        } catch (\InvalidArgumentException $e) {
            return Redirect::back()->withErrors(__('messages.' . $e->getMessage()));
        }

        /**
         * Getting current authenticated user
         */
        $user = Auth::user();

        /**
         * Getting same basic channel informations
         */
        try {
            $channelName = YoutubeChannelCheckingService::getChannelName($channel_id);
        } catch (\InvalidArgumentException $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }
        
        /**
         * Channel creating
         */
        try {
            $channel = Channel::create([
                'user_id' => $user->user_id,
                'channel_id' => $channel_id,
                'channel_name' => $channelName,
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('message', __('messages.flash_channel_id_is_invalid'));
            $request->session()->flash('messageClass', 'alert-danger');
        }

        /**
         * Creating subscription on one free plan (default)
         * We will update it once paid.
         */
        try {
            Subscription::create([
                'channel_id' => $channel_id,
                'plan_id' => Plan::_FREE_PLAN_ID,
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('message', __('messages.flash_subscription_creation_has_failed'));
            $request->session()->flash('messageClass', 'alert-danger');
        }

        /**
         * Sending congratulations mail
         */
        Mail::to($user)->send(new ChannelIsRegistered($user, $channel));

        /**
         * All went fine
         */
        $request->session()->flash('message', __('messages.flash_channel_has_been_created', ['channel' => $channelName]));
        $request->session()->flash('messageClass', 'alert-success');

        /**
         * Redirect to home if free plan
         */
        return redirect()->route('home');
    }
}
