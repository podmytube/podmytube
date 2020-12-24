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

use App\Factories\ChannelCreationFactory;
use App\Http\Requests\ChannelCreationRequest;
use App\Plan;
use Exception;
use Illuminate\Support\Facades\Auth;

class ChannelCreateController extends Controller
{
    /** @var App\Youtube\YoutubeChannel $youtubeChannelObj */
    protected $youtubeChannelObj;

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
    public function store(ChannelCreationRequest $request)
    {
        $validatedParams = $request->validated();
        try {
            $factory = ChannelCreationFactory::create(Auth::user(), $validatedParams['channel_url'], Plan::bySlug('forever_free'));
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->withErrors(['danger' => $exception->getMessage()]);
        }
        return redirect()->route('home')->with('success', "Channel {$factory->channel()->channel_name} has been successfully registered.");
    }
}
