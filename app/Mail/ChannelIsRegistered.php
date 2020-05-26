<?php

namespace App\Mail;

use App\Channel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelIsRegistered extends Mailable
{
    use Queueable, SerializesModels;

    protected $channel;

    /**
     * Create a new message instance.
     *
     * @return void
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
        return $this->view('emails.channelRegistered')->with([
            'user' => $this->channel->user,
            'channel' => $this->channel,
        ]);
    }
}
