<?php

namespace App\Http\Controllers;

use App\Channel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Swift_Message;

class MailViewerController extends Controller
{
    public function monthlyReport(Request $request, Channel $channel)
    {
        $period = date(__('config.monthPeriodFormat'));
        $subject = __('emails.monthlyReport_subject', [
            'period' => $period,
            'channel_name' => $channel->channel_name,
        ]);

        $publishedMedias = $channel->medias()
            ->publishedLastMonth()
            ->get();

        $lastMonth = Carbon::now()->subMonth();

        return view('emails.monthlyReport')
            ->with([
                'message' => (new Message(new Swift_Message())),
                'mailTitle' => $subject,
                'period' => $period,
                'channel' => $channel,
                'publishedMedias' => $publishedMedias,
                'shouldChannelBeUpgraded' => $channel->shouldChannelBeUpgraded($lastMonth->month, $lastMonth->year),
            ]);
    }
}
