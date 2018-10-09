<?php
/**
 * the home controller. Only used to display the welcome page project for now.
 * 
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\ThumbService;

/**
 * the home controller class.
 * 
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $channels = Auth::user()->channels;
        foreach($channels as $channel) {
            /**
             * Retrieve thumbs relation
             */
            $thumb = $channel->thumbs;

            /**
             * Getting vignette
             */
            try {
                $channel->isDefaultVignette = false;
                $channel->vigUrl = ThumbService::getChannelVignetteUrl($thumb);
            } catch (\Exception $e) {
                $channel->isDefaultVignette = true;
                $channel->vigUrl = ThumbService::getDefaultVignetteUrl();
            }
        }
        
        return view('home', compact('channels'));
    }
}
