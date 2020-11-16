<?php

namespace App\Mail;

use App\Channel;
use App\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * This mailable is sent on every first day of month.
 * It will display a list of medias for the channel. Those grabbed and not grabbed.
 * With a call to action : upgrade your plan (if one (at least)) episode is not grabbed.
 */
class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var App\Channel $channel */
    protected $channel;

    /** @var Illuminate\Support\Collection $publishedMedias */
    protected $publishedMedias = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
        $this->medias = $channel->medias()->publishedLastMonth();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $period = date(__('config.monthPeriodFormat'));
        $subject = __('emails.monthlyReport_subject', [
            'period' => $period,
            'channel_name' => $this->channel->channel_name,
        ]);

        $this->publishedMedias = $this->channel
            ->medias()
            ->publishedLastMonth()
            ->orderBy('published_at', 'desc')
            ->get();

        return $this->subject($subject)
            ->view('emails.monthlyReport')
            ->with([
                'mailTitle' => $subject,
                'channel' => $this->channel,
                'publishedMedias' => $this->publishedMedias,
                'shouldChannelBeUpgraded' => $this->shouldChannelBeUpgraded(),
            ]);
    }

    protected function shouldChannelBeUpgraded()
    {
        if ($this->channel->subscription->plan->id == Plan::FREE_PLAN_ID) {
            return true;
        }

        if (
            $this->publishedMedias->count() >
            $this->channel->subscription->plan->nb_episodes_per_month
        ) {
            return true;
        }

        return false;
    }
}
