<?php

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Services\ChannelService;
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
        try {
            $channels = ChannelService::getAuthenticatedUserChannels(
                Auth::user()
            );
        } catch (\Exception $e) {
            $channels = collect([]);
        }

        return view('home', compact('channels'));
    }
}
