<?php

namespace App\Http\Controllers;

use Auth;

use App\Channel;

use App\AppStats;

use Illuminate\Http\Request;

class AppStatsController extends Controller
{
    protected $default_nb_days_to_retrieve = 7;
    /**
     * mainly useful to guard some routes
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * get list of user's channel
     * 
     * @param Channel Channel object
     * @return Response
     */
    public function index(Channel $channel, Request $request)
    {
        $user = Auth::user();

        // **************************************************************
        // CHEAT - TODO - USE Policies
        // **************************************************************
        if ($user->user_id != $channel->user_id) {

            throw new \Exception(" you shouldn't be there !!! ");
            
        }
        // **************************************************************

        $results = AppStats::channelId($channel->channel_id);
        
        if ( NULL !== $request->query('lastdays')) 
        {
            // lastdays parameters is set
        
            $results = $results->lastNDays($request->query('lastdays'));
            
        } elseif (NULL !== $request->query('start') && NULL !== $request->query('end')) {

            // we have a start date and a end date, grab the stats between

            $results = $results->betweenDays([$request->query('start'), $request->query('end')]);

        } else {

            // default page, we are taking some last days stats

            $results->lastNDays($this->default_nb_days_to_retrieve);

        }

        $results->select(\DB::raw('channel_id, ua_appName, sum(app_cpt) as sum_cpt'));
        
        $results->join('uas', 'app_stats.ua_id', '=', 'uas.id');

        $results->groupBy('uas.ua_appName', 'channel_id');

        $results->orderBy('sum_cpt', 'desc');
        
        $pie_results = $results->get();

        $sum_app_cpt_for_period = $pie_results->pluck('sum_cpt')->sum();
        
        $pie_results->transform(function($item) use ($sum_app_cpt_for_period) {
            //dd($item->app_cpt);
            $item['percentage'] = round($item['sum_cpt'] / $sum_app_cpt_for_period * 100,2);
            return $item;
        });
        
        return view('app_stats.index', 
            compact(
                'channel', 
                'pie_results'
            ));

    }
}
