<?php

declare(strict_types=1);

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Channel;

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
        $nbActiveChannels = Channel::active()
            ->whereHas('medias', function ($query): void {
                $query->whereNotNull('grabbed_at')
                    ->whereBetween('grabbed_at', [
                        now()->startOfMonth(),
                        now(),
                    ])
                ;
            })
            ->count()
        ;

        return view('cockpit.index', compact('lastRegisteredChannel', 'nbActiveChannels'));
    }
}
