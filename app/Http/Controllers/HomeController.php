<?php

declare(strict_types=1);

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Download;
use App\Models\Playlist;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * the home controller class.
 */
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var Authenticatable $user */
        $user = Auth::user();

        /**
         * Get user's channel(s).
         */
        /* $channels = Channel::query()
            ->select('user_id', 'channel_id', 'channel_name', 'podcast_title', 'active')
            ->where('user_id', '=', $user->id())
            ->with([
                'playlists:channel_id,active',
                'subscription:channel_id,plan_id',
                'subscription.plan:id,name',
            ])
            ->get()
        ; */
        $channels = Channel::with(['subscription.plan', 'cover'])
            ->where('user_id', '=', $user->id())
            ->get()
            ->map(function ($channel) {
                $channel->thisWeekDownloads = Download::sumOfDownloadsForChannelDuringPeriod(
                    $channel,
                    now()->startOfWeek(weekStartsAt: Carbon::MONDAY),
                    now()->endOfWeek(weekEndsAt: Carbon::SUNDAY),
                );

                $channel->thisMonthDownloads = Download::sumOfDownloadsForChannelDuringPeriod(
                    $channel,
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                );

                return $channel;
            })
        ;

        $playlists = Playlist::userPlaylists($user);

        return view('home', compact('user', 'channels', 'playlists'));
    }
}
