<?php

namespace App\Mail;

use App\Channel;
use Carbon\Carbon;
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

    /** @var \App\Channel $channel */
    protected $channel;

    /** @var \Carbon\Carbon $wantedMonth */
    protected $wantedMonth;

    /** @var \Illuminate\Support\Collection $publishedMedias */
    protected $publishedMedias = [];

    /**
     * Create a monthly report email.
     *
     * @param \App\Channel $channel
     * @param \Carbon\Carbon wanted month (start of month)
     *
     * @return void
     */
    public function __construct(Channel $channel, Carbon $wantedMonth)
    {
        $this->channel = $channel;
        $this->wantedMonth = $wantedMonth;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /** formatted period is Month Year (IE : march 2021) */
        $formattedPeriod = $this->wantedMonth->format(__('config.monthPeriodFormat'));
        $subject = __('emails.monthlyReport_subject', [
            'period' => $formattedPeriod,
            'channel_name' => $this->channel->channel_name,
        ]);
        $endOfMonth = (clone $this->wantedMonth)->endOfMonth();
        $this->wantedMonth->startOfMonth()->subDay();

        $this->publishedMedias = $this->channel->medias()
            ->publishedBetween($this->wantedMonth, $endOfMonth)
            ->orderBy('published_at', 'desc')
            ->get();

        return $this->subject($subject)
            ->view('emails.monthlyReport')
            ->with([
                'mailTitle' => $subject,
                'formattedPeriod' => $formattedPeriod,
                'channel' => $this->channel,
                'publishedMedias' => $this->publishedMedias,
                'shouldChannelBeUpgraded' => $this->channel->shouldChannelBeUpgraded($this->wantedMonth->month, $this->wantedMonth->year),
            ]);
    }
}
