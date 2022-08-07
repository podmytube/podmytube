<?php

declare(strict_types=1);

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Factories\RevenueFactory;
use App\Factories\VolumeOnDiskFactory;
use App\Models\Channel;
use App\Models\Media;

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
        $nbPodcasts = Channel::active()->count();

        $nbMedias = Media::whereNotNull('grabbed_at')->count();

        $revenues = RevenueFactory::init()->get();

        $volumeOnDisk = VolumeOnDiskFactory::init()->formatted();

        return view('cockpit.index', compact(
            'lastRegisteredChannel',
            'nbPodcasts',
            'nbActiveChannels',
            'nbMedias',
            'revenues',
            'volumeOnDisk',
        ));
    }
}
