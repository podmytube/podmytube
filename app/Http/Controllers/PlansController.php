<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;


class PlansController extends Controller
{
    /**
     * Show the available plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {   
        return view('plans.index', compact('channel'));
    }
}
