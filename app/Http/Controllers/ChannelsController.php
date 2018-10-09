<?php

/**
 * the channel controller
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChannelRequest;
use App\Services\ChannelService;

use App\Channel;

/**
 * the channel controller class.
 */
class ChannelsController extends Controller
{
	/**
	 * mainly useful to guard some routes
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * get list of user's channel
	 * @return Response
	 */
	public function index()
	{
		$channels = ChannelService::getAuthenticatedUserChannels(Auth::user());

        return view('channel.index', compact('channels'));

	}

	/**
	 * display all informations about one channel
	 * 
	 * @param  ChannelRequest  	$request 
	 * @param  Channel  		$channel
	 * @return Response*
	 */

	public function show(ChannelRequest $request, Channel $channel)
	{

		$channel->podcast_url = \App\Channel::podcastUrl($channel);
		$channel->youtube_url = \App\Channel::youtubeUrl($channel);
		//dd($channel->channel_id);

		return view('channel.show', compact('channel'));

	}

	/**
	 * display the channel form in order to edit channel data
	 *
	 * @param  ChannelRequest          $request
	 * @param  Channel              $channel
	 * @return Response
	 */
	public function edit(ChannelRequest $request, Channel $channel)
	{

		return view('channel.edit', compact('channel'));

	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  ChannelRequest $request the form request 
	 * @param  Channel $channel channel concerned
	 * @return Response
	 */
	public function update(ChannelRequest $request, Channel $channel)
	{
		//$channel = Channel::findOrFail($id);

		$channel->update($request->all());

		\Session::flash('message', 'Channel successfully updated !');
		\Session::flash('alert-class', 'alert-success');

		return redirect('channel/' . $channel->channel_id);

	}
}
