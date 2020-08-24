<?php

namespace App\Http\Controllers;

/**
 * the home controller class.
 */
class IndexController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * With no DB Config, creating plans list
         */
        $plans = [
            'free' => [
                'name' => 'Free forever',
                'nb_episodes_per_month' => 2,
                'price' => 0,
            ],
            'weekly' => [
                'name' => 'Weekly Youtuber',
                'nb_episodes_per_month' => 10,
                'price' => 9,
            ],
            'daily' => [
                'name' => 'Daily Youtuber',
                'nb_episodes_per_month' => 33,
                'price' => 29,
            ],
        ];

        /**
         * convert hash table into many variables I can compact
         */

        return view('index', compact(['plans']));
    }
}
