<?php

declare(strict_types=1);

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Channel;
use App\Factories\RevenueFactory;
use App\Media;

/**
 * the home controller class.
 */
class CockpitController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lastRegisteredChannel = Channel::orderBy('channel_createdAt', 'desc')->first();

        $nbActiveChannels = Channel::nbReallyActiveChannels();

        $nbMedias = Media::whereNotNull('grabbed_at')->count();

        $revenues = RevenueFactory::init()->get();

        return view('cockpit.index', compact('lastRegisteredChannel', 'nbActiveChannels', 'nbMedias', 'revenues'));
    }
}
