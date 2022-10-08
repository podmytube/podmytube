<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * This mailable is sent on every first day of month.
 * It will display a list of medias for the channel. Those grabbed and not grabbed.
 * With a call to action : upgrade your plan (if one (at least)) episode is not grabbed.
 */
class MonthlyReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public const DATE_MONTH_YEAR_FORMAT = 'F Y';
    public Collection $publishedMedias;
    public string $formattedPeriod;
    public bool $displayUpgradeMessage = false;

    protected ?Carbon $wantedMonth;

    /**
     * Create a monthly report email.
     */
    public function __construct(public Channel $channel, ?Carbon $wantedMonth = null)
    {
        $this->wantedMonth = $wantedMonth ?? now();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // formatted period is Month Year (IE : march 2021)
        $this->formattedPeriod = $this->wantedMonth->format(self::DATE_MONTH_YEAR_FORMAT);
        $this->wantedMonth->startOfMonth();
        $endOfMonth = (clone $this->wantedMonth)->endOfMonth();

        $this->publishedMedias = $this->channel->medias()
            ->whereNotNull('media_id')
            ->orderBy('published_at', 'desc')
            ->publishedBetween($this->wantedMonth, $endOfMonth)
            ->get()
        ;
        $this->displayUpgradeMessage = $this->channel->shouldChannelBeUpgraded($this->wantedMonth->month, $this->wantedMonth->year);

        return $this->view('emails.monthlyReport')
            ->subject($this->getSubject())
            ->with([
                'mailTitle' => $this->getSubject(),
            ])
        ;
    }

    public function getSubject(): string
    {
        return "Here is your {$this->wantedMonth->format(self::DATE_MONTH_YEAR_FORMAT)} report for {$this->channel->channel_name}";
    }
}
