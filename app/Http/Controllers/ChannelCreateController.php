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

use App\User;
use App\Channel;
use App\Mail\ChannelIsRegistered;
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
        return view('channel.create');
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
         * Channel creating
         */
        try {
            $channel = Channel::create([
                'user_id' => $user->user_id,
                'channel_id' => $channel_id,
                'channel_name' => __('messages.channel_to_be_validated')
            ]);
        } catch (\Exception $e) { 
            $request->session()->flash('message',__('messages.flash_channel_id_is_invalid'));
            $request->session()->flash('messageClass','alert-danger');
        }
        //$subscription = Subscription::create($request);

        /**
         * Sending congratulations mail
         */
        Mail::to($user)->send(new ChannelIsRegistered($user, $channel));

        /**
         * All went fine
         */
        $request->session()->flash('message',__('messages.flash_channel_has_been_created', ['channel' => $channel_id]));
        $request->session()->flash('messageClass','alert-success');
        
        /**
         * Redirect to home
         */
        return redirect()->route('home');
    }
}
