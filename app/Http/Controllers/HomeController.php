<?php

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Channel;
use App\Modules\Vignette;
use App\Playlist;
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
         * Get user's channel(s)
         */
        $channels = Channel::with(['subscription.plan', 'cover'])
            ->where('user_id', '=', Auth::id())
            ->get();
        $channels = $channels->map(function ($channel) {
            $channel->vignetteUrl = Vignette::defaultUrl();
            if ($channel->cover) {
                $channel->vignetteUrl = Vignette::fromThumb($channel->cover)->url();
            }
            return $channel;
        });

        $playlists = Playlist::userPlaylists(Auth::user());

        return view('home', compact('channels', 'playlists'));
    }
}
