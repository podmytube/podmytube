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
use App\Models\Download;
use App\Models\Media;
use Illuminate\Contracts\View\View;

/**
 * the home controller class.
 */
class CockpitController extends Controller
{
    public function index(): View
    {
        $lastRegisteredChannel = Channel::orderBy('channel_createdAt', 'desc')->first();

        $nbPodcasts = Channel::active()->count();

        $nbMedias = Media::whereNotNull('grabbed_at')->count();

        $revenues = RevenueFactory::init()->get();

        $volumeOnDisk = VolumeOnDiskFactory::init()->formatted();

        $monthDownloads = Download::downloadsDuringPeriod(now()->startOfmonth(), now());

        return view('cockpit.index', compact(
            'lastRegisteredChannel',
            'nbPodcasts',
            'nbMedias',
            'revenues',
            'volumeOnDisk',
            'monthDownloads',
        ));
    }
}
