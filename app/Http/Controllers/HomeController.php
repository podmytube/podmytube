<?php

declare(strict_types=1);

/**
 * the home controller. Only used to display the welcome page project for now.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Services\HomeDetailsService;
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
    public function index(HomeDetailsService $service)
    {
        /** @var Authenticatable $user */
        $user = Auth::user();

        $channels = $service->userContent($user);

        $playlists = Playlist::userPlaylists($user);

        return view('home', compact('user', 'channels', 'playlists'));
    }
}
