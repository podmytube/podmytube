<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\User;
use App\Channel;
use App\Subscription;
/**
 * This class is used when a channel is created
 */
class CreateChannelService
{
    public function create(Request $request)
    {
        /**
         * The only field required is the channel id. We are asking for the url channel
         * It should be 26 characters long too contain at least http://youtube.com/channel/
         */
        $request->validate([

            'channel_url' => 'required|string|min:27',

        ]);

        if (!$channel_id = Channel::extractChannelIdFromUrl($request->channel_url)) {
            return Redirect::back()->withErrors([__('messages.flash_channel_id_is_invalid')]);
        }

        /**
         * Channel creating
         */
        $channel = Channel::create([
            'user_id' => \Auth::user()->user_id,
            'channel_id' => $channel_id,
            'channel_name' => __('messages.channel_to_be_validated'),
            'youtube_channel_id' => $channel_id,
        ]);
        
        $subscription = Subscription::create($request);

        /**
         * Getting current authenticated user
         */
        $user = User::find(\Auth::user()->user_id);

        /**
         * Sending congratulations mail
         */
        Mail::to($user)->send(new ChannelIsRegistered($user, $channel));
    }
}
