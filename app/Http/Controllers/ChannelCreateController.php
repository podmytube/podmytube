<?php

declare(strict_types=1);

/**
 * the channel create controller.
 *
 * this controller is handling the new channel form part.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Factories\CreateChannelFactory;
use App\Http\Requests\ChannelCreationRequest;
use App\Models\Channel;
use App\Models\Plan;
use App\Models\Subscription;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChannelCreateController extends Controller
{
    public const DEFAULT_CATEGORY_SLUG = 'society-culture';

    /** @var App\Youtube\YoutubeChannel */
    protected $youtubeChannelObj;

    /**
     * Display the form channel creation.
     * The form only ask for youtube channel url.
     */
    public function step1()
    {
        return view('channel.create');
    }

    /**
     * Validate the youtube url received and create inactive channel.
     */
    public function step1Validate(ChannelCreationRequest $request): RedirectResponse
    {
        $validatedParams = $request->validated();
        $youtubeUrl = $validatedParams['channel_url'];

        try {
            $channel = DB::transaction(function () use ($youtubeUrl) {
                $channel = CreateChannelFactory::fromYoutubeUrl(Auth::user(), $youtubeUrl);

                Subscription::query()
                    ->updateOrCreate(
                        ['channel_id' => $channel->channelId()],
                        [
                            'channel_id' => $channel->channelId(),
                            'plan_id' => Plan::bySlug('forever_free')->id,
                            'ends_at' => null,
                        ]
                    )
                ;

                return $channel;
            });

            return redirect()->route('channel.step2', $channel);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return redirect()->back()->withErrors([
                'danger' => 'Podcast registration has failed. Please send me an email about it at fred@podmytube.com',
            ]);
        }
    }

    /**
     * Allow user to select the plan he will subscribe to.
     */
    public function step2(Request $request, Channel $channel)
    {
        $isYearly = $request->get('yearly') === '1' ? true : false;

        $plans = Plan::bySlugsAndBillingFrequency(['starter', 'professional', 'business'], $isYearly);

        // foreach plan create a session id that will be associated with plan
        $plans->map(function (Plan $plan) use ($channel): void {
            $plan->addStripeSessionForChannel($channel);
        });

        $buttonLabel = 'Register';
        $routeName = 'channel.step2';

        return view(
            'channel.step2',
            compact('routeName', 'channel', 'plans', 'isYearly', 'buttonLabel')
        );
    }
}
