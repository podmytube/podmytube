<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelIsRegistered extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $channel;

    /**
     * Create a new message instance.
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.channelRegistered')
            ->with([
                'user' => $this->channel->user,
                'channel' => $this->channel,
            ])
        ;
    }
}
