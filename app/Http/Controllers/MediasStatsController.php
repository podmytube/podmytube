<?php

namespace App\Http\Controllers;

use App\Channel;

use App\MediasStats;

use Illuminate\Http\Request;

use Auth;

class MediasStatsController extends Controller
{
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
        //$user = auth()->user();
        $user = Auth::user();

        // **************************************************************
        // CHEAT - TODO - USE Policies
        // **************************************************************
        if ($user->user_id != $channel->user_id) {

            throw new \Exception(" you shouldn't be there !!! ");
            
        }
        // **************************************************************

        
        if ( NULL !== $request->query('lastdays')) 
        {
            // lastdays parameters is set
        
            $results = MediasStats::channelId($channel->channel_id)->lastNDays($request->query('lastdays'));
            
        } elseif (NULL !== $request->query('start') && NULL !== $request->query('end')) {

            // we have a start date and a end date, grab the stats between

            $results = MediasStats::channelId($channel->channel_id)->betweenDays([$request->query('start'), $request->query('end')]);

        } else {

            // default page, we are taking some last days stats

            $results = MediasStats::channelId($channel->channel_id)->lastNDays(7);

        }

        $results->select(\DB::raw('channel_id, media_day, sum(media_cpt) as sum_cpt'));
       
        $results->groupBy('media_day', 'channel_id');
    
        $results->orderBy('media_day', 'asc')->get();
        
        $nb_downloads_X = $results->pluck('media_day')->all();
        $nb_downloads_Y = $results->pluck('sum_cpt')->all();
       
        
        return view('medias_stats.index', 
            compact(
                'channel', 
                'nb_downloads_X', 
                'nb_downloads_Y', 
                'active_period', 
                'valid_periods'
            ));

    }
}
