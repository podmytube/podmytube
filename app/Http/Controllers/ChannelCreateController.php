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

use App\ApiKey;
use App\Channel;
use App\Events\ChannelRegistered;
use App\Exceptions\ChannelCreationHasFailedException;
use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\SubscriptionHasFailedException;
use App\Factories\ChannelCreationFactory;
use App\Http\Requests\ChannelCreationRequest;
use App\Modules\YoutubeChannelId;
use App\Plan;
use App\Quota;
use App\Subscription;
use App\Youtube\YoutubeChannel;
use App\Youtube\YoutubeQuotas;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
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
            $factory = ChannelCreationFactory::create($this->user, $validatedParams['channel_url']);
        } catch (\Exception $exception) {
            redirect()->back()->withErrors(['message' => $exception->getMessage()]);
        }

        return redirect()->route('home')->with('success', "Channel {$factory->channel()->name} has been successfully registered.");
    }

    /**
     * will persist quota consumption.
     */
    protected function updateQuotaConsumption()
    {
        $apikeysAndQuotas = YoutubeQuotas::forUrls(
            $this->youtubeChannelObj->queriesUsed()
        )->quotaConsumed();
        array_walk($apikeysAndQuotas, function ($quota, $apikey) {
            Quota::create([
                'apikey_id' => ApiKey::where('apikey', '=', $apikey)->first()
                    ->id,
                'script' => pathinfo(__FILE__, PATHINFO_BASENAME),
                'quota_used' => $quota,
                'created_at' => Carbon::now(),
            ]);
        });
    }
}
