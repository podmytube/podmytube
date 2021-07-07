<?php

declare(strict_types=1);

/**
 * the channel controller.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Category;
use App\Channel;
use App\Events\ChannelUpdated;
use App\Http\Requests\ChannelRequest;
use App\Jobs\ChannelCleaningJob;
use App\Language;

/**
 * the channel controller class.
 */
class ChannelsController extends Controller
{
    /**
     * display all informations about one channel.
     *
     * @param ChannelRequest $request
     *
     * @return Response*
     */
    public function show(Channel $channel)
    {
        $this->authorize($channel);

        return view('channel.show', compact('channel'));
    }

    /**
     * display the channel form in order to edit channel data.
     *
     * @param ChannelRequest $request
     *
     * @return Response
     */
    public function edit(Channel $channel)
    {
        $this->authorize($channel);
        $categories = Category::list();
        $languages = Language::get();

        return view('channel.edit', compact(['channel', 'categories', 'languages']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ChannelRequest $request the form request
     * @param Channel        $channel channel concerned
     *
     * @return Response
     */
    public function update(ChannelRequest $request, Channel $channel)
    {
        $this->authorize($channel);

        $channel->update($request->validated());

        ChannelUpdated::dispatch($channel);

        return redirect(route('home'))
            ->with(
                'success',
                "Your podcast {$channel->title()} has been successfully updated !"
            )
        ;
    }

    public function destroy(Channel $channel)
    {
        $this->authorize($channel);

        $savedTitle = $channel->podcastTitle();

        ChannelCleaningJob::dispatch($channel);

        return redirect(route('home'))
            ->with(
                'success',
                "Your podcast {$savedTitle} is planned for deletion."
            )
        ;
    }
}
