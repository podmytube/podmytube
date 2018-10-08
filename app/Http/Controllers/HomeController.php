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
/**
 * the channel controller class.
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
             * retrieve path to thumbnail
             */
        }

        return view('home', compact('channels'));
    }
}
