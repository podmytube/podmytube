<?php

declare(strict_types=1);

namespace App\Mail;

use App\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelHasReachedItsLimitsMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Channel $channel)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailTitle = "Your channel {$this->channel->nameWithId()} has exceeded its monthly quota.";

        return $this->view('emails.channelHasReachedItsLimits')
            ->subject($mailTitle)
            ->with([
                'channel' => $this->channel,
                'userName' => $this->channel->user->name,
                'mailTitle' => "Your channel {$this->channel->nameWithId()} has exceeded its monthly quota.",
            ])
        ;
    }
}
