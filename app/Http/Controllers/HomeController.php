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
use App\Modules\Vignette;
use Carbon\Carbon;
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
        /**
         * Get user's channel(s).
         */
        $channels = Channel::with(['subscription.plan', 'cover'])
            ->where('user_id', '=', Auth::id())
            ->get()
            ->map(function ($channel) {
                $channel->vignetteUrl = Vignette::defaultUrl();
                if ($channel->cover) {
                    $channel->vignetteUrl = Vignette::fromThumb($channel->cover)->url();
                }

                $channel->thisWeekDownloads = Download::downloadsForChannelDuringPeriod(
                    $channel,
                    now()->startOfWeek(weekStartsAt: Carbon::MONDAY),
                    now()->endOfWeek(weekEndsAt: Carbon::SUNDAY),
                );

                $channel->thisMonthDownloads = Download::downloadsForChannelDuringPeriod(
                    $channel,
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                );

                return $channel;
            })
        ;

        $playlists = Playlist::userPlaylists(Auth::user());

        return view('home', compact('channels', 'playlists'));
    }
}
