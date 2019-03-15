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
use App\User;
use App\Channel;
use App\Mail\ChannelIsRegistered;
use App\Services\CreateChannelService;
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
     */
    public function store(Request $request)
    {
        try {
            CreateChannelService::create($request);
        } catch (\Exception $e) {
            session()->flash('message', __('messages.creation_channel_has_failed'));
            session()->flash('messageClass', 'alert-info');
        }        

        /**
         * Displaying congratulations and redirect 
         */
        session()->flash('status', __('messages.flash_channel_has_been_created'));
        return redirect('/home');
    }
}
