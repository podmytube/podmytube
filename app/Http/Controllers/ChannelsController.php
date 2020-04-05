<?php

/**
 * the channel controller
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Category;
use App\Channel;
use App\Events\ChannelUpdated;
use App\Http\Requests\ChannelRequest;
use App\Services\ChannelService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * the channel controller class.
 */
class ChannelsController extends Controller
{
    /**
     * get list of user's channel
     * @return Response
     */
    public function index()
    {
        try {
            $channels = ChannelService::getAuthenticatedUserChannels(
                Auth::user()
            );
        } catch (\Exception $e) {
            $channels = [];
        }
        return view('channel.index', compact('channels'));
    }

    /**
     * display all informations about one channel
     *
     * @param  ChannelRequest      $request
     * @return Response*
     */

    public function show(Channel $channel)
    {
        $this->authorize($channel);
        return view('channel.show', compact('channel'));
    }

    /**
     * display the channel form in order to edit channel data
     *
     * @param  ChannelRequest          $request
     * @param  Channel              $channel
     * @return Response
     */
    public function edit(Channel $channel)
    {
        $this->authorize($channel);
        $categories = Category::list();
        return view('channel.edit', compact(['channel', 'categories']));
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
        $this->authorize($channel);
        $validatedParams = $request->validated();
        $validatedParams['explicit'] = $request->has('explicit') ? 1 : 0;

        $channel->update($validatedParams);

        event(new ChannelUpdated($channel));

        \Session::flash('message', 'Channel successfully updated !');
        \Session::flash('alert-class', 'alert-success');

        return redirect('channel/' . $channel->channel_id);
    }
}
