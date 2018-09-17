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
use Auth;
use App\Channel;
use App\User;
use App\Mail\ChannelIsRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

class ChannelCreateController extends Controller
{
    /**
     * Display the form channel creation
     *
     * @return void
     */

    public function create()
    {
        return view('channel.create');
    }

    /**
     * create one channel from the form received
     *
     * @return void
     * @todo remove youtube_channel_id USELESS but mandatory
     */
    public function store()
    {
        /**
         * The only field required is the channel id. We are asking for the url channel
         * It should be 26 characters long too contain at least http://youtube.com/channel/
         */
        $this->validate(request(), [

            'channel_url' => 'required|string|min:27'

        ]);

        if (!$channel_id = Channel::extractChannelIdFromUrl(request('channel_url'))) {
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

        /**
         * Getting current authenticated user
         */
        $user = User::find(\Auth::user()->user_id);
        
        /**
         * Sending congratulations mail 
         */
        Mail::to($user)->send(new ChannelIsRegistered($user, $channel));

        /**
         * Displaying congratulations and redirect 
         */
        request()->session()->flash('status', __('messages.flash_channel_has_been_created'));
        return redirect('/home');
    }
}
